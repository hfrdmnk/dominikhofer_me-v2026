#!/usr/bin/env node

/**
 * Migration script for extracting trailing hashtags from existing tweets
 *
 * Scans note.txt files in content/notes subdirectories with Tags: tweet
 * Extracts trailing hashtags from Body, adds them to Tags, removes from Body
 *
 * Usage: node scripts/migrate-hashtags.js [--dry-run]
 */

const fs = require('fs');
const path = require('path');

const NOTES_DIR = path.join(__dirname, '..', 'content', 'notes');
const DRY_RUN = process.argv.includes('--dry-run');

/**
 * Parse a Kirby content file into key-value pairs
 */
function parseKirbyContent(content) {
  const fields = {};
  const parts = content.split(/\n----\n/);

  for (const part of parts) {
    const trimmed = part.trim();
    if (!trimmed) continue;

    const colonIndex = trimmed.indexOf(':');
    if (colonIndex === -1) continue;

    const key = trimmed.substring(0, colonIndex).trim();
    const value = trimmed.substring(colonIndex + 1).trim();
    fields[key] = value;
  }

  return fields;
}

/**
 * Serialize fields back to Kirby content format
 */
function serializeKirbyContent(fields) {
  const parts = [];
  const fieldOrder = ['Title', 'Date', 'Tags', 'Media_1', 'Media_2', 'Media_3', 'Media_4', 'Body'];

  for (const key of fieldOrder) {
    if (fields[key] !== undefined) {
      parts.push(`${key}: ${fields[key]}`);
    }
  }

  // Add any remaining fields not in the order
  for (const [key, value] of Object.entries(fields)) {
    if (!fieldOrder.includes(key) && value !== undefined) {
      parts.push(`${key}: ${value}`);
    }
  }

  return parts.join('\n\n----\n\n') + '\n';
}

/**
 * Extract trailing hashtags from text
 * Returns: { text: string, tags: string[] }
 */
function extractTrailingHashtags(text) {
  const tags = [];

  // Match trailing hashtags (at end of text, separated by whitespace/newlines)
  const match = text.match(/(\s*(#\w+))+\s*$/);

  if (match) {
    const trailingPart = match[0];

    // Extract individual hashtags
    const tagMatches = trailingPart.match(/#(\w+)/g);
    if (tagMatches) {
      for (const tag of tagMatches) {
        tags.push(tag.substring(1).toLowerCase()); // Remove # and lowercase
      }
    }

    // Remove trailing hashtags from text
    text = text.substring(0, text.length - trailingPart.length).trimEnd();
  }

  return { text, tags };
}

/**
 * Process a single note file
 */
function processNoteFile(filePath) {
  const content = fs.readFileSync(filePath, 'utf8');
  const fields = parseKirbyContent(content);

  // Skip if not a tweet
  const tags = (fields.Tags || '').toLowerCase();
  if (!tags.includes('tweet')) {
    return { skipped: true, reason: 'not a tweet' };
  }

  // Skip if no body
  if (!fields.Body) {
    return { skipped: true, reason: 'no body' };
  }

  // Extract trailing hashtags
  const result = extractTrailingHashtags(fields.Body);

  if (result.tags.length === 0) {
    return { skipped: true, reason: 'no trailing hashtags' };
  }

  // Merge extracted tags with existing tags
  const existingTags = fields.Tags
    ? fields.Tags.split(',').map(t => t.trim().toLowerCase())
    : [];

  const newTags = [...new Set([...existingTags, ...result.tags])];

  // Update fields
  fields.Body = result.text;
  fields.Tags = newTags.join(', ');

  // Write back
  if (!DRY_RUN) {
    const newContent = serializeKirbyContent(fields);
    fs.writeFileSync(filePath, newContent);
  }

  return {
    modified: true,
    extractedTags: result.tags,
    newTags: newTags
  };
}

/**
 * Main migration function
 */
function migrate() {
  console.log(`Migration script for extracting trailing hashtags from tweets`);
  console.log(`Mode: ${DRY_RUN ? 'DRY RUN (no changes will be made)' : 'LIVE'}`);
  console.log('');

  if (!fs.existsSync(NOTES_DIR)) {
    console.error(`Notes directory not found: ${NOTES_DIR}`);
    process.exit(1);
  }

  const noteFolders = fs.readdirSync(NOTES_DIR);
  let processed = 0;
  let modified = 0;
  let skipped = 0;

  for (const folder of noteFolders) {
    const noteFile = path.join(NOTES_DIR, folder, 'note.txt');

    if (!fs.existsSync(noteFile)) {
      continue;
    }

    processed++;
    const result = processNoteFile(noteFile);

    if (result.skipped) {
      skipped++;
    } else if (result.modified) {
      modified++;
      console.log(`[MODIFIED] ${folder}`);
      console.log(`  Extracted tags: ${result.extractedTags.join(', ')}`);
      console.log(`  New tags: ${result.newTags.join(', ')}`);
    }
  }

  console.log('');
  console.log(`Summary:`);
  console.log(`  Processed: ${processed}`);
  console.log(`  Modified: ${modified}`);
  console.log(`  Skipped: ${skipped}`);

  if (DRY_RUN && modified > 0) {
    console.log('');
    console.log('Run without --dry-run to apply changes.');
  }
}

migrate();
