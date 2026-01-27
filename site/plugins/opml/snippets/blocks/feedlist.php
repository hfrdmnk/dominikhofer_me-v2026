<?php
/** @var \Kirby\Cms\Block $block */

use Opml\OpmlParser;

// Find the OPML file in page attachments
$opmlFile = $block->parent()->files()->filterBy('extension', 'opml')->first();
if (!$opmlFile) {
  return;
}

$sections = OpmlParser::parse($opmlFile->root());
if (empty($sections)) {
  return;
}

// Sort sections alphabetically by title
usort($sections, fn($a, $b) => strcasecmp($a['title'], $b['title']));
?>

<div class="feedlist">
  <?php foreach ($sections as $section): ?>
    <?php if ($section['type'] === 'section' && !empty($section['feeds'])): ?>
      <?php
        // Sort feeds alphabetically within section
        $feeds = $section['feeds'];
        usort($feeds, fn($a, $b) => strcasecmp($a['title'], $b['title']));
      ?>

      <h2><?= esc($section['title']) ?></h2>

      <ul class="feedlist-items">
        <?php foreach ($feeds as $feed): ?>
          <li class="feedlist-item">
            <span class="inline-flex items-center gap-1">
              <a href="<?= esc($feed['htmlUrl'] ?: $feed['xmlUrl']) ?>" class="feedlist-link" target="_blank" rel="noopener">
                <?= esc($feed['title']) ?>
              </a>
              <?php if ($feed['xmlUrl']): ?>
                <a href="<?= esc($feed['xmlUrl']) ?>" class="feedlist-rss" target="_blank" rel="noopener" title="RSS Feed">
                  <?php snippet('icon', ['name' => 'rss', 'class' => 'size-4 text-muted']) ?>
                </a>
              <?php endif ?>
            </span>
          </li>
        <?php endforeach ?>
      </ul>
    <?php endif ?>
  <?php endforeach ?>
</div>
