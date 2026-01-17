const fs = require('fs');
const path = require('path');
const { loadTweets } = require('./parser');
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
 * Import a single tweet to Kirby
 * @param {Object} tweet - Tweet object from parser
 * @returns {string} Created folder path
 */
function importTweet(tweet) {
  const uuid = generateUuid();
  const folderName = `${tweet.datePrefix}_${uuid}`;
  const folderPath = path.join(NOTES_FOLDER, folderName);

  // Create folder
  fs.mkdirSync(folderPath, { recursive: true });

  // Create note.txt content
  const content = createKirbyContent({
    title: uuid,
    date: tweet.kirbyDate,
    tags: 'tweet',
    body: tweet.text,
  });

  fs.writeFileSync(path.join(folderPath, 'note.txt'), content);

  // Copy media files
  for (const mediaPath of tweet.mediaFiles) {
    const filename = path.basename(mediaPath);
    // Remove the tweet ID prefix from filename for cleaner naming
    const cleanFilename = filename.replace(`${tweet.id}-`, '');
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

  console.log(`Importing ${selectedIds.length} selected tweets...`);

  const tweets = loadTweets();
  const selectedTweets = tweets.filter(t => selectedIds.includes(t.id));

  let imported = 0;
  let withMedia = 0;

  for (const tweet of selectedTweets) {
    const folderPath = importTweet(tweet);
    imported++;
    if (tweet.hasMedia) withMedia++;
    console.log(`  [${imported}/${selectedTweets.length}] Imported: ${path.basename(folderPath)}`);
  }

  console.log(`\nDone! Imported ${imported} tweets (${withMedia} with media).`);

  return { imported, withMedia };
}

module.exports = {
  importTweet,
  importSelected,
  generateUuid,
  createKirbyContent,
};
