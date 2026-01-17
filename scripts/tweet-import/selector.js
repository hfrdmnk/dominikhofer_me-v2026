const fs = require('fs');
const path = require('path');
const readline = require('readline');
const { loadTweets } = require('./parser');

const STATE_FILE = path.join(__dirname, 'state.json');

/**
 * Load state from file
 * @returns {Object} State object
 */
function loadState() {
  if (fs.existsSync(STATE_FILE)) {
    return JSON.parse(fs.readFileSync(STATE_FILE, 'utf-8'));
  }
  return {
    position: 0,
    deselected: [],
  };
}

/**
 * Save state to file
 * @param {Object} state
 */
function saveState(state) {
  fs.writeFileSync(STATE_FILE, JSON.stringify(state, null, 2));
}

/**
 * Clear the terminal
 */
function clearScreen() {
  process.stdout.write('\x1B[2J\x1B[0f');
}

/**
 * Wrap text to specified width
 * @param {string} text
 * @param {number} width
 * @returns {string}
 */
function wrapText(text, width) {
  const words = text.split(' ');
  const lines = [];
  let currentLine = '';

  for (const word of words) {
    if (currentLine.length + word.length + 1 <= width) {
      currentLine += (currentLine ? ' ' : '') + word;
    } else {
      if (currentLine) lines.push(currentLine);
      currentLine = word;
    }
  }
  if (currentLine) lines.push(currentLine);

  return lines.join('\n');
}

/**
 * Display a tweet in the terminal
 * @param {Object} tweet
 * @param {number} index
 * @param {number} total
 * @param {number} selectedCount
 * @param {boolean} isDeselected
 */
function displayTweet(tweet, index, total, selectedCount, isDeselected) {
  const width = Math.min(process.stdout.columns || 60, 60);
  const divider = '─'.repeat(width);

  clearScreen();

  // Header line
  const positionStr = `Tweet ${index + 1}/${total}`;
  const selectedStr = `Selected: ${selectedCount}`;
  const padding = width - positionStr.length - selectedStr.length;
  console.log(positionStr + ' '.repeat(Math.max(padding, 2)) + selectedStr);

  console.log(divider);

  // Date and media indicator
  const mediaIndicator = tweet.hasMedia ? '[📷]' : '';
  const selectIndicator = isDeselected ? '[✗]' : '';
  const indicators = [selectIndicator, mediaIndicator].filter(Boolean).join(' ');
  const datePadding = width - tweet.displayDate.length - indicators.length;
  console.log(tweet.displayDate + ' '.repeat(Math.max(datePadding, 2)) + indicators);

  console.log('');

  // Tweet text (wrapped)
  const wrappedText = wrapText(tweet.text, width);
  console.log(wrappedText);

  console.log('');
  console.log(divider);

  // Controls
  console.log('← prev | → next | ENTER deselect | S save & quit | Q quit');
}

/**
 * Run the selector UI
 */
async function runSelector() {
  console.log('Loading tweets...');
  const tweets = loadTweets();
  const state = loadState();

  console.log(`Found ${tweets.length} top-level tweets.`);
  console.log(`Starting from position ${state.position + 1}, ${tweets.length - (state.deselected?.length || 0)} selected.`);
  console.log('Press any key to start...');

  // Enable raw mode for keypress detection
  readline.emitKeypressEvents(process.stdin);
  if (process.stdin.isTTY) {
    process.stdin.setRawMode(true);
  }

  let currentIndex = state.position;

  const render = () => {
    const tweet = tweets[currentIndex];
    const isDeselected = state.deselected.includes(tweet.id);
    const selectedCount = tweets.length - state.deselected.length;
    displayTweet(tweet, currentIndex, tweets.length, selectedCount, isDeselected);
  };

  // Wait for initial keypress
  await new Promise(resolve => {
    process.stdin.once('keypress', resolve);
  });

  render();

  process.stdin.on('keypress', (str, key) => {
    if (key.name === 'q' || (key.ctrl && key.name === 'c')) {
      // Quit without saving
      clearScreen();
      console.log('Exited without saving.');
      process.exit(0);
    }

    if (key.name === 's') {
      // Save and quit
      state.position = currentIndex;
      saveState(state);
      clearScreen();
      const selectedCount = tweets.length - state.deselected.length;
      console.log(`Saved! Position: ${currentIndex + 1}, Selected: ${selectedCount} tweets.`);
      console.log('\nRun `node scripts/tweet-import import` to import selected tweets.');
      process.exit(0);
    }

    if (key.name === 'left') {
      // Previous tweet
      if (currentIndex > 0) {
        currentIndex--;
        state.position = currentIndex;
        saveState(state);
        render();
      }
    }

    if (key.name === 'right') {
      // Next tweet
      if (currentIndex < tweets.length - 1) {
        currentIndex++;
        state.position = currentIndex;
        saveState(state);
        render();
      }
    }

    if (key.name === 'return') {
      // Toggle deselection
      const tweet = tweets[currentIndex];
      const idx = state.deselected.indexOf(tweet.id);
      if (idx === -1) {
        state.deselected.push(tweet.id);  // Deselect
      } else {
        state.deselected.splice(idx, 1);  // Re-select
      }
      saveState(state);
      render();
    }
  });
}

/**
 * Get current selection stats
 * @returns {Object}
 */
function getStats() {
  const state = loadState();
  const tweets = loadTweets();
  return {
    total: tweets.length,
    selected: tweets.length - state.deselected.length,
    deselected: state.deselected.length,
    position: state.position,
  };
}

/**
 * Get selected tweet IDs
 * @returns {Array}
 */
function getSelectedIds() {
  const state = loadState();
  const tweets = loadTweets();
  // Return all IDs except deselected ones
  return tweets
    .map(t => t.id)
    .filter(id => !state.deselected.includes(id));
}

module.exports = {
  runSelector,
  getStats,
  getSelectedIds,
  loadState,
};
