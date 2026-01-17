#!/usr/bin/env node

const { runSelector, getStats } = require('./selector');
const { importSelected } = require('./importer');

const command = process.argv[2];

async function main() {
  switch (command) {
    case 'import':
      importSelected();
      break;

    case 'status':
      const stats = getStats();
      console.log('Tweet Import Status');
      console.log('───────────────────');
      console.log(`Total tweets:    ${stats.total}`);
      console.log(`Selected:        ${stats.selected}`);
      console.log(`Current position: ${stats.position + 1}`);
      break;

    case 'help':
    case '--help':
    case '-h':
      console.log('Tweet Import Tool');
      console.log('');
      console.log('Usage:');
      console.log('  node scripts/tweet-import          Start the selector UI');
      console.log('  node scripts/tweet-import import   Import selected tweets to Kirby');
      console.log('  node scripts/tweet-import status   Show selection stats');
      console.log('');
      console.log('Selector Controls:');
      console.log('  ← / →     Navigate between tweets');
      console.log('  ENTER     Toggle selection');
      console.log('  S         Save and quit');
      console.log('  Q         Quit without saving');
      break;

    default:
      // Default: run selector
      await runSelector();
      break;
  }
}

main().catch(err => {
  console.error('Error:', err.message);
  process.exit(1);
});
