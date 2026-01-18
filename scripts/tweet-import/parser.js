const fs = require('fs');
const path = require('path');

const TWITTER_EXPORT = 'TWITTER_EXPORT_PATH';
const TWEETS_FILE = path.join(TWITTER_EXPORT, 'data/tweets.js');
const MEDIA_FOLDER = path.join(TWITTER_EXPORT, 'data/tweets_media');
const MY_USER_ID = '960963811636404225';

/**
 * Parse Twitter export tweets.js file
 * @returns {Array} Array of all tweets
 */
function parseTweetsFile() {
  const content = fs.readFileSync(TWEETS_FILE, 'utf-8');
  // Strip the `window.YTD.tweets.part0 = ` prefix
  const jsonStr = content.replace(/^window\.YTD\.tweets\.part0\s*=\s*/, '');
  const tweets = JSON.parse(jsonStr);
  return tweets.map(t => t.tweet);
}

/**
 * Find media files for a tweet in the tweets_media folder
 * @param {string} tweetId - Tweet ID
 * @returns {Array} Array of media file paths
 */
function findMediaFiles(tweetId) {
  if (!fs.existsSync(MEDIA_FOLDER)) return [];

  const files = fs.readdirSync(MEDIA_FOLDER);
  const mediaFiles = files.filter(f => f.startsWith(`${tweetId}-`));
  return mediaFiles.map(f => path.join(MEDIA_FOLDER, f));
}

/**
 * Parse Twitter date format to Date object
 * @param {string} dateStr - Twitter date string
 * @returns {Date}
 */
function parseTwitterDate(dateStr) {
  return new Date(dateStr);
}

/**
 * Format Date to human readable (for display)
 * @param {Date} date
 * @returns {string}
 */
function formatDisplayDate(date) {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                  'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
}

/**
 * Format Date for Kirby content file
 * @param {Date} date
 * @returns {string}
 */
function formatKirbyDate(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

/**
 * Format Date for folder prefix
 * @param {Date} date
 * @returns {string}
 */
function formatDatePrefix(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}${month}${day}`;
}

/**
 * Clean tweet text (expand t.co URLs and remove media links)
 * @param {Object} tweet - Tweet object
 * @returns {string}
 */
function cleanTweetText(tweet) {
  let text = tweet.full_text;

  // Replace t.co URLs with expanded URLs
  if (tweet.entities && tweet.entities.urls) {
    for (const urlObj of tweet.entities.urls) {
      text = text.replace(urlObj.url, urlObj.expanded_url);
    }
  }

  // Remove t.co media URLs (they're just links to attached media)
  if (tweet.entities && tweet.entities.media) {
    for (const media of tweet.entities.media) {
      text = text.replace(media.url, '').trim();
    }
  }

  // Replace Twitter/X URLs with xcancel.com for privacy
  text = text.replace(/twitter\.com/g, 'xcancel.com');
  text = text.replace(/x\.com/g, 'xcancel.com');

  return text.trim();
}

/**
 * Check if a tweet is a reply to yourself (part of a thread)
 * @param {Object} tweet - Raw tweet object
 * @returns {boolean}
 */
function isReplyToSelf(tweet) {
  return tweet.in_reply_to_user_id_str === MY_USER_ID;
}

/**
 * Check if a tweet is a reply to someone else
 * @param {Object} tweet - Raw tweet object
 * @returns {boolean}
 */
function isReplyToOthers(tweet) {
  return tweet.in_reply_to_status_id_str && !isReplyToSelf(tweet);
}

/**
 * Check if a tweet is a retweet
 * @param {Object} tweet - Raw tweet object
 * @returns {boolean}
 */
function isRetweet(tweet) {
  return tweet.full_text.startsWith('RT @');
}

/**
 * Build threads from all tweets
 * Groups connected tweets where you replied to yourself
 * @param {Array} allTweets - All raw tweets
 * @returns {Array} Array of thread objects
 */
function buildThreads(allTweets) {
  // Create a map of tweet ID to tweet
  const tweetMap = new Map();
  for (const tweet of allTweets) {
    tweetMap.set(tweet.id_str, tweet);
  }

  // Track which tweets are part of a thread (as non-first tweets)
  const usedInThread = new Set();

  // Build threads: find chains where you replied to yourself
  const threads = [];

  for (const tweet of allTweets) {
    // Skip retweets
    if (isRetweet(tweet)) continue;

    // Skip replies to others
    if (isReplyToOthers(tweet)) continue;

    // Skip if this tweet is already used as a continuation of another thread
    if (usedInThread.has(tweet.id_str)) continue;

    // This is either a standalone tweet or the START of a thread
    // A tweet starts a thread if it's not a reply to self
    if (isReplyToSelf(tweet)) continue;

    // Find all replies that continue this thread
    const threadTweets = [tweet];
    let currentTweetId = tweet.id_str;

    // Look for replies to this tweet (and subsequent replies)
    let foundContinuation = true;
    while (foundContinuation) {
      foundContinuation = false;
      for (const candidate of allTweets) {
        if (candidate.in_reply_to_status_id_str === currentTweetId && isReplyToSelf(candidate)) {
          threadTweets.push(candidate);
          usedInThread.add(candidate.id_str);
          currentTweetId = candidate.id_str;
          foundContinuation = true;
          break;
        }
      }
    }

    threads.push(threadTweets);
  }

  return threads;
}

/**
 * Transform a thread (array of tweets) into a unified thread object
 * @param {Array} tweets - Array of raw tweets in chronological order (first tweet first)
 * @returns {Object} Thread object
 */
function transformThread(tweets) {
  const firstTweet = tweets[0];
  const date = parseTwitterDate(firstTweet.created_at);

  // Collect all media files from all tweets
  const allMediaFiles = [];
  const firstTweetMedia = findMediaFiles(firstTweet.id_str);

  for (const tweet of tweets) {
    const media = findMediaFiles(tweet.id_str);
    allMediaFiles.push(...media);
  }

  // Build combined text with separators
  let combinedText;
  if (tweets.length === 1) {
    combinedText = cleanTweetText(firstTweet);
  } else {
    const textParts = [];
    for (let i = 0; i < tweets.length; i++) {
      const tweet = tweets[i];
      let text = cleanTweetText(tweet);

      // For non-first tweets, add inline images for their media
      if (i > 0) {
        const tweetMedia = findMediaFiles(tweet.id_str);
        for (const mediaPath of tweetMedia) {
          const filename = path.basename(mediaPath).replace(`${tweet.id_str}-`, '');
          text += `\n\n(image: ${filename})`;
        }
      }

      textParts.push(text);
    }
    combinedText = textParts.join('\n\n---\n\n');
  }

  return {
    id: firstTweet.id_str,
    isThread: tweets.length > 1,
    tweetCount: tweets.length,
    tweetIds: tweets.map(t => t.id_str),
    text: combinedText,
    date: date,
    displayDate: formatDisplayDate(date),
    kirbyDate: formatKirbyDate(date),
    datePrefix: formatDatePrefix(date),
    hasMedia: allMediaFiles.length > 0,
    mediaFiles: allMediaFiles,
    firstTweetMedia: firstTweetMedia,
  };
}

/**
 * Load and parse all tweets, grouped into threads
 * @returns {Array} Array of thread objects (single tweets are threads of 1)
 */
function loadTweets() {
  const allTweets = parseTweetsFile();
  const threads = buildThreads(allTweets);

  // Transform each thread
  const transformed = threads.map(transformThread);

  // Sort by date (newest first)
  transformed.sort((a, b) => b.date - a.date);

  return transformed;
}

/**
 * Load all raw tweets (for debugging/inspection)
 * @returns {Array} Array of raw tweet objects
 */
function loadAllRawTweets() {
  return parseTweetsFile();
}

module.exports = {
  loadTweets,
  loadAllRawTweets,
  parseTwitterDate,
  formatDisplayDate,
  formatKirbyDate,
  formatDatePrefix,
  findMediaFiles,
  TWITTER_EXPORT,
  MEDIA_FOLDER,
  MY_USER_ID,
};
