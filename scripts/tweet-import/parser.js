const fs = require('fs');
const path = require('path');

const TWITTER_EXPORT = 'TWITTER_EXPORT_PATH';
const TWEETS_FILE = path.join(TWITTER_EXPORT, 'data/tweets.js');
const MEDIA_FOLDER = path.join(TWITTER_EXPORT, 'data/tweets_media');

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
 * Filter to only top-level tweets (not replies or retweets)
 * @param {Array} tweets - All tweets
 * @returns {Array} Top-level tweets only
 */
function filterTopLevel(tweets) {
  return tweets.filter(tweet => {
    // Exclude replies
    if (tweet.in_reply_to_status_id || tweet.in_reply_to_status_id_str) {
      return false;
    }
    // Exclude retweets (start with "RT @")
    if (tweet.full_text.startsWith('RT @')) {
      return false;
    }
    return true;
  });
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
 * Example: "Mon Jun 10 08:40:19 +0000 2024"
 * @param {string} dateStr - Twitter date string
 * @returns {Date}
 */
function parseTwitterDate(dateStr) {
  return new Date(dateStr);
}

/**
 * Format Date to human readable (for display)
 * @param {Date} date
 * @returns {string} e.g. "Feb 16, 2024"
 */
function formatDisplayDate(date) {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                  'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  return `${months[date.getMonth()]} ${date.getDate()}, ${date.getFullYear()}`;
}

/**
 * Format Date for Kirby content file
 * @param {Date} date
 * @returns {string} e.g. "2024-02-16 14:30:00"
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
 * @returns {string} e.g. "20240216"
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
 * @returns {string} Cleaned text
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

  return text;
}

/**
 * Transform raw tweet into simplified format
 * @param {Object} tweet - Raw tweet object
 * @returns {Object} Simplified tweet
 */
function transformTweet(tweet) {
  const date = parseTwitterDate(tweet.created_at);
  const mediaFiles = findMediaFiles(tweet.id_str);

  return {
    id: tweet.id_str,
    text: cleanTweetText(tweet),
    rawText: tweet.full_text,
    date: date,
    displayDate: formatDisplayDate(date),
    kirbyDate: formatKirbyDate(date),
    datePrefix: formatDatePrefix(date),
    hasMedia: mediaFiles.length > 0,
    mediaFiles: mediaFiles,
  };
}

/**
 * Load and parse all top-level tweets
 * @returns {Array} Array of simplified tweet objects
 */
function loadTweets() {
  const allTweets = parseTweetsFile();
  const topLevel = filterTopLevel(allTweets);

  // Sort by date (newest first)
  const sorted = topLevel.sort((a, b) => {
    const dateA = parseTwitterDate(a.created_at);
    const dateB = parseTwitterDate(b.created_at);
    return dateB - dateA;
  });

  return sorted.map(transformTweet);
}

module.exports = {
  loadTweets,
  parseTwitterDate,
  formatDisplayDate,
  formatKirbyDate,
  formatDatePrefix,
  TWITTER_EXPORT,
  MEDIA_FOLDER,
};
