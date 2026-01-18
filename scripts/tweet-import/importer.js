const fs = require('fs');
const path = require('path');
const { loadTweets, findMediaFiles } = require('./parser');
const { getSelectedIds } = require('./selector');

const NOTES_FOLDER = 'PROJECT_ROOT/content/notes';

/**
 * Generate 16-character lowercase alphanumeric UUID
 * @returns {string}
 */
function generateUuid() {
  const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
  let result = '';
  for (let i = 0; i < 16; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
}

/**
 * Get clean filename (remove tweet ID prefix)
 * @param {string} mediaPath - Full path to media file
 * @param {string} tweetId - Tweet ID to strip
 * @returns {string} Clean filename
 */
function getCleanFilename(mediaPath, tweetId) {
  const filename = path.basename(mediaPath);
  return filename.replace(`${tweetId}-`, '');
}

/**
 * Create Kirby content file
 * @param {Object} fields - Key-value pairs for content
 * @returns {string}
 */
function createKirbyContent(fields) {
  const parts = [];
  for (const [key, value] of Object.entries(fields)) {
    if (value !== undefined && value !== null && value !== '') {
      // Capitalize first letter of key
      const kirbyKey = key.charAt(0).toUpperCase() + key.slice(1);
      parts.push(`${kirbyKey}: ${value}`);
    }
  }
  return parts.join('\n\n----\n\n') + '\n';
}

/**
 * Import a single tweet/thread to Kirby
 * @param {Object} thread - Thread object from parser
 * @returns {string} Created folder path
 */
function importTweet(thread) {
  const uuid = generateUuid();
  const folderName = `${thread.datePrefix}_${uuid}`;
  const folderPath = path.join(NOTES_FOLDER, folderName);

  // Create folder
  fs.mkdirSync(folderPath, { recursive: true });

  // Determine media slots (first tweet's media, up to 4)
  const mediaSlots = {};
  const firstTweetMedia = thread.firstTweetMedia || [];

  for (let i = 0; i < Math.min(4, firstTweetMedia.length); i++) {
    const mediaPath = firstTweetMedia[i];
    // Extract the tweet ID from the path to clean the filename
    const filename = path.basename(mediaPath);
    const tweetId = filename.split('-')[0];
    const cleanFilename = getCleanFilename(mediaPath, tweetId);
    mediaSlots[`media_${i + 1}`] = cleanFilename;
  }

  // Create note.txt content
  const content = createKirbyContent({
    title: uuid,
    date: thread.kirbyDate,
    tags: 'tweet',
    media_1: mediaSlots.media_1,
    media_2: mediaSlots.media_2,
    media_3: mediaSlots.media_3,
    media_4: mediaSlots.media_4,
    body: thread.text,
  });

  fs.writeFileSync(path.join(folderPath, 'note.txt'), content);

  // Copy all media files from all tweets in thread
  for (const mediaPath of thread.mediaFiles) {
    const filename = path.basename(mediaPath);
    const tweetId = filename.split('-')[0];
    const cleanFilename = getCleanFilename(mediaPath, tweetId);
    fs.copyFileSync(mediaPath, path.join(folderPath, cleanFilename));
  }

  return folderPath;
}

/**
 * Import all selected tweets
 * @returns {Object} Stats about imported tweets
 */
function importSelected() {
  const selectedIds = getSelectedIds();

  if (selectedIds.length === 0) {
    console.log('No tweets selected. Run the selector first.');
    return { imported: 0 };
  }

  console.log(`Importing ${selectedIds.length} selected tweets/threads...`);

  const tweets = loadTweets();
  const selectedTweets = tweets.filter(t => selectedIds.includes(t.id));

  let imported = 0;
  let withMedia = 0;
  let threads = 0;

  for (const tweet of selectedTweets) {
    const folderPath = importTweet(tweet);
    imported++;
    if (tweet.hasMedia) withMedia++;
    if (tweet.isThread) threads++;
    const threadInfo = tweet.isThread ? ` [thread: ${tweet.tweetCount} tweets]` : '';
    console.log(`  [${imported}/${selectedTweets.length}] Imported: ${path.basename(folderPath)}${threadInfo}`);
  }

  console.log(`\nDone! Imported ${imported} items (${withMedia} with media, ${threads} threads).`);

  return { imported, withMedia, threads };
}

module.exports = {
  importTweet,
  importSelected,
  generateUuid,
  createKirbyContent,
};
