#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

// ============================================================================
// Configuration
// ============================================================================

const OLD_PROJECT = 'OLD_PROJECT';
const NEW_PROJECT = 'PROJECT_ROOT';

const PATHS = {
  oldPosts: path.join(OLD_PROJECT, 'src/content/posts'),
  oldNewsletter: path.join(OLD_PROJECT, 'src/content/newsletter'),
  oldTil: path.join(OLD_PROJECT, 'src/content/til'),
  oldRaces: path.join(OLD_PROJECT, 'src/content/races'),
  oldPhotos: path.join(OLD_PROJECT, 'src/content/photos'),
  oldAssets: path.join(OLD_PROJECT, 'src/assets'),
  newPosts: path.join(NEW_PROJECT, 'content/posts'),
  newNotes: path.join(NEW_PROJECT, 'content/notes'),
  newRaces: path.join(NEW_PROJECT, 'content/races'),
  newPhotos: path.join(NEW_PROJECT, 'content/photos'),
};

// ============================================================================
// Utilities
// ============================================================================

/**
 * Parse YAML frontmatter from MDX/MD file content
 */
function parseFrontmatter(content) {
  const match = content.match(/^---\n([\s\S]*?)\n---\n([\s\S]*)$/);
  if (!match) return { data: {}, content: content };

  const frontmatter = match[1];
  const body = match[2].trim();

  const data = {};
  let currentKey = null;
  let inArray = false;

  const lines = frontmatter.split('\n');

  for (let i = 0; i < lines.length; i++) {
    const line = lines[i];

    // Check for array item (starts with spaces + dash)
    if (/^\s+-\s/.test(line) && currentKey) {
      inArray = true;
      const value = line.replace(/^\s+-\s*/, '').trim();
      if (!Array.isArray(data[currentKey])) {
        data[currentKey] = [];
      }
      if (value) data[currentKey].push(value);
      continue;
    }

    // Check for continuation line (starts with spaces, not a new key)
    if (/^\s+\S/.test(line) && currentKey && !inArray) {
      // This is a continuation of the previous value
      const trimmed = line.trim();
      if (typeof data[currentKey] === 'string') {
        data[currentKey] += ' ' + trimmed;
      }
      continue;
    }

    // Check for new key
    const keyMatch = line.match(/^(\w+):\s*(.*)$/);
    if (keyMatch) {
      currentKey = keyMatch[1];
      inArray = false;
      let value = keyMatch[2].trim();

      // Handle quoted strings (remove surrounding quotes)
      if ((value.startsWith('"') && value.endsWith('"')) ||
          (value.startsWith("'") && value.endsWith("'"))) {
        value = value.slice(1, -1);
      }

      // Handle boolean and empty values
      if (value === 'true') {
        data[currentKey] = true;
      } else if (value === 'false') {
        data[currentKey] = false;
      } else if (value === '' || value === '>-' || value === '|' || value === '>') {
        // Multiline indicator - value will come from continuation lines
        data[currentKey] = '';
      } else {
        data[currentKey] = value;
      }
    }
  }

  return { data, content: body };
}

/**
 * Convert ISO date string to Kirby format
 */
function formatDate(dateStr, includeTime = true) {
  if (!dateStr) return '';

  const date = new Date(dateStr);
  if (isNaN(date.getTime())) return '';

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');

  if (!includeTime) {
    return `${year}-${month}-${day}`;
  }

  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

/**
 * Get Kirby folder number prefix from date
 */
function getFolderNum(dateStr) {
  if (!dateStr) return Date.now().toString();

  const date = new Date(dateStr);
  if (isNaN(date.getTime())) return Date.now().toString();

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');

  return `${year}${month}${day}${hours}${minutes}`;
}

/**
 * Get Unix timestamp from date
 */
function getUnixTimestamp(dateStr) {
  if (!dateStr) return Math.floor(Date.now() / 1000).toString();
  const date = new Date(dateStr);
  return Math.floor(date.getTime() / 1000).toString();
}

/**
 * Slugify a string
 */
function slugify(str) {
  return str
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '-')
    .replace(/^-|-$/g, '');
}

/**
 * Generate 16-character lowercase alphanumeric UUID
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
 * Get date prefix in YYYYMMDD format (no time component)
 */
function getDatePrefix(dateStr) {
  if (!dateStr) {
    const now = new Date();
    return `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}`;
  }
  const date = new Date(dateStr);
  if (isNaN(date.getTime())) {
    const now = new Date();
    return `${now.getFullYear()}${String(now.getMonth() + 1).padStart(2, '0')}${String(now.getDate()).padStart(2, '0')}`;
  }
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}${month}${day}`;
}

/**
 * Format tags as lowercase, comma-separated
 */
function formatTags(tags) {
  if (!tags) return '';
  if (Array.isArray(tags)) {
    return tags.map(t => t.toLowerCase()).join(', ');
  }
  return tags.toLowerCase();
}

/**
 * Strip MDX imports and components from content
 */
function stripMdx(content) {
  // Remove import statements
  let cleaned = content.replace(/^import\s+.*$/gm, '');

  // Remove JSX components (simple approach - remove self-closing tags)
  cleaned = cleaned.replace(/<[A-Z][a-zA-Z]*[^>]*\/>/g, '');

  // Remove opening/closing JSX component tags
  cleaned = cleaned.replace(/<[A-Z][a-zA-Z]*[^>]*>[\s\S]*?<\/[A-Z][a-zA-Z]*>/g, '');

  // Clean up multiple blank lines
  cleaned = cleaned.replace(/\n{3,}/g, '\n\n');

  return cleaned.trim();
}

/**
 * Create Kirby content file
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
 * Copy image file if it exists
 */
function copyImage(sourcePath, destFolder, newFilename) {
  // Handle relative path from content file
  if (sourcePath.startsWith('../../assets/')) {
    const assetPath = sourcePath.replace('../../assets/', '');
    const fullPath = path.join(OLD_PROJECT, 'src/assets', assetPath);

    if (fs.existsSync(fullPath)) {
      const destPath = path.join(destFolder, newFilename || path.basename(fullPath));
      fs.copyFileSync(fullPath, destPath);
      return path.basename(destPath);
    }
  }
  return null;
}

/**
 * Find and copy image from assets folder
 */
function findAndCopyImage(collection, slug, destFolder) {
  const assetFolder = path.join(PATHS.oldAssets, collection, slug);

  if (fs.existsSync(assetFolder)) {
    const files = fs.readdirSync(assetFolder);
    for (const file of files) {
      if (/\.(jpg|jpeg|png|gif|webp)$/i.test(file)) {
        const sourcePath = path.join(assetFolder, file);
        // Use a clean filename
        const ext = path.extname(file);
        const destPath = path.join(destFolder, `cover${ext}`);
        fs.copyFileSync(sourcePath, destPath);
        return `cover${ext}`;
      }
    }
  }
  return null;
}

// ============================================================================
// Migration Functions
// ============================================================================

function migratePosts() {
  console.log('\n📝 Migrating posts...');

  const files = fs.readdirSync(PATHS.oldPosts).filter(f => f.endsWith('.mdx'));
  let count = 0;

  for (const file of files) {
    const filePath = path.join(PATHS.oldPosts, file);
    const raw = fs.readFileSync(filePath, 'utf-8');
    const { data, content } = parseFrontmatter(raw);

    const slug = file.replace('.mdx', '');
    const folderNum = getFolderNum(data.date);
    const folderName = `${folderNum}_${slug}`;
    const destFolder = path.join(PATHS.newPosts, folderName);

    // Create folder
    fs.mkdirSync(destFolder, { recursive: true });

    // Copy image if exists
    if (data.visual) {
      copyImage(data.visual, destFolder, 'cover' + path.extname(data.visual));
    }

    // Create content file
    const kirbyContent = createKirbyContent({
      title: data.title || slug,
      date: formatDate(data.date),
      updated: data.lastUpdated ? formatDate(data.lastUpdated) : '',
      excerpt: data.excerpt || '',
      tags: formatTags(data.tags),
      content: stripMdx(content),
    });

    fs.writeFileSync(path.join(destFolder, 'post.txt'), kirbyContent);
    count++;
  }

  console.log(`   ✓ Migrated ${count} posts`);
}

function migrateNewsletter() {
  console.log('\n📬 Migrating newsletter issues...');

  const files = fs.readdirSync(PATHS.oldNewsletter).filter(f => f.endsWith('.mdx'));
  let count = 0;

  for (const file of files) {
    const filePath = path.join(PATHS.oldNewsletter, file);
    const raw = fs.readFileSync(filePath, 'utf-8');
    const { data, content } = parseFrontmatter(raw);

    const issueNum = file.replace('.mdx', '');
    const folderNum = getFolderNum(data.date);
    const folderName = `${folderNum}_tiny-sparks-${issueNum}`;
    const destFolder = path.join(PATHS.newPosts, folderName);

    // Create folder
    fs.mkdirSync(destFolder, { recursive: true });

    // Copy image if exists
    if (data.visual) {
      copyImage(data.visual, destFolder, 'cover' + path.extname(data.visual));
    }

    // Create content file
    const kirbyContent = createKirbyContent({
      title: data.title || `Tiny Sparks #${issueNum}`,
      date: formatDate(data.date),
      excerpt: data.excerpt || '',
      tags: 'tiny sparks',
      content: stripMdx(content),
    });

    fs.writeFileSync(path.join(destFolder, 'post.txt'), kirbyContent);
    count++;
  }

  console.log(`   ✓ Migrated ${count} newsletter issues`);
}

function migrateTil() {
  console.log('\n💡 Migrating TIL entries...');

  const files = fs.readdirSync(PATHS.oldTil).filter(f => f.endsWith('.md'));
  let count = 0;

  for (const file of files) {
    const filePath = path.join(PATHS.oldTil, file);
    const raw = fs.readFileSync(filePath, 'utf-8');
    const { data, content } = parseFrontmatter(raw);

    const uuid = generateUuid();
    const datePrefix = getDatePrefix(data.date);
    const folderName = `${datePrefix}_${uuid}`;
    const destFolder = path.join(PATHS.newNotes, folderName);

    // Create folder
    fs.mkdirSync(destFolder, { recursive: true });

    // Build content with title and source
    let noteContent = '';
    if (data.title) {
      noteContent += `**${data.title}**\n\n`;
    }
    noteContent += content.trim();
    if (data.source) {
      noteContent += `\n\nSource: ${data.source}`;
    }

    // Create content file
    const kirbyContent = createKirbyContent({
      title: uuid,
      date: formatDate(data.date),
      tags: 'til',
      body: noteContent,
    });

    fs.writeFileSync(path.join(destFolder, 'note.txt'), kirbyContent);
    count++;
  }

  console.log(`   ✓ Migrated ${count} TIL entries`);
}

function migrateRaces() {
  console.log('\n🏃 Migrating races...');

  const files = fs.readdirSync(PATHS.oldRaces).filter(f => f.endsWith('.md'));
  let count = 0;

  for (const file of files) {
    const filePath = path.join(PATHS.oldRaces, file);
    const raw = fs.readFileSync(filePath, 'utf-8');
    const { data } = parseFrontmatter(raw);

    const slug = file.replace('.md', '');
    const folderNum = getFolderNum(data.date + 'T12:00:00');
    const folderName = `${folderNum}_${slug}`;
    const destFolder = path.join(PATHS.newRaces, folderName);

    // Create folder
    fs.mkdirSync(destFolder, { recursive: true });

    // Create content file
    const kirbyContent = createKirbyContent({
      title: data.title || slug,
      date: formatDate(data.date, false),
      location: data.location || '',
      distance: data.distance || '',
      time: data.time || '',
      pace: data.pace || '',
      tags: '',
    });

    fs.writeFileSync(path.join(destFolder, 'race.txt'), kirbyContent);
    count++;
  }

  console.log(`   ✓ Migrated ${count} races`);
}

function migratePhotos() {
  console.log('\n📷 Migrating photos...');

  const files = fs.readdirSync(PATHS.oldPhotos).filter(f => f.endsWith('.md'));
  let count = 0;

  for (const file of files) {
    const filePath = path.join(PATHS.oldPhotos, file);
    const raw = fs.readFileSync(filePath, 'utf-8');
    const { data } = parseFrontmatter(raw);

    const uuid = generateUuid();
    const datePrefix = getDatePrefix(data.date);
    const slug = file.replace('.md', '');
    const folderName = `${datePrefix}_${uuid}`;
    const destFolder = path.join(PATHS.newPhotos, folderName);

    // Create folder
    fs.mkdirSync(destFolder, { recursive: true });

    // Copy image
    if (data.visual) {
      copyImage(data.visual, destFolder, 'photo' + path.extname(data.visual));
    } else {
      // Try to find image in assets
      findAndCopyImage('photos', slug, destFolder);
    }

    // Create content file
    const kirbyContent = createKirbyContent({
      title: uuid,
      date: formatDate(data.date),
      location: data.location || '',
      body: '',
      tags: '',
    });

    fs.writeFileSync(path.join(destFolder, 'photo.txt'), kirbyContent);
    count++;
  }

  console.log(`   ✓ Migrated ${count} photos`);
}

// ============================================================================
// Main
// ============================================================================

function main() {
  console.log('🚀 Starting migration from Astro to Kirby...\n');
  console.log(`   Old project: ${OLD_PROJECT}`);
  console.log(`   New project: ${NEW_PROJECT}`);

  // Verify paths exist
  if (!fs.existsSync(OLD_PROJECT)) {
    console.error('❌ Old project not found!');
    process.exit(1);
  }

  if (!fs.existsSync(NEW_PROJECT)) {
    console.error('❌ New project not found!');
    process.exit(1);
  }

  // Run migrations
  migratePosts();
  migrateNewsletter();
  migrateTil();
  migrateRaces();
  migratePhotos();

  console.log('\n✅ Migration complete!');
  console.log('\nNext steps:');
  console.log('1. Run "composer start" in the new project');
  console.log('2. Open http://localhost:8000/panel');
  console.log('3. Verify content appears correctly');
  console.log('4. Manually assign cover images if needed');
}

main();
