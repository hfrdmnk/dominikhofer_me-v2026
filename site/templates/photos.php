<?php
/**
 * Photos listing template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$feed = $page->children()
  ->listed()
  ->sortBy('date', 'desc')
  ->paginate(50);
?>
<?php snippet('layouts/base', slots: true) ?>
  <div class="px-4 py-8">
    <?php if ($feed->isNotEmpty()): ?>
      <?php foreach ($feed as $item): ?>
        <?php snippet('feed-item', ['item' => $item]) ?>
      <?php endforeach ?>

      <?php if ($feed->pagination()->hasPages()): ?>
      <nav class="mt-8 flex justify-center gap-4">
        <?php if ($feed->pagination()->hasPrevPage()): ?>
        <a href="<?= $feed->pagination()->prevPageUrl() ?>" class="rounded-medium border border-border px-4 py-2 text-sm transition-colors hover:border-accent">
          Newer
        </a>
        <?php endif ?>
        <?php if ($feed->pagination()->hasNextPage()): ?>
        <a href="<?= $feed->pagination()->nextPageUrl() ?>" class="rounded-medium border border-border px-4 py-2 text-sm transition-colors hover:border-accent">
          Older
        </a>
        <?php endif ?>
      </nav>
      <?php endif ?>
    <?php else: ?>
      <p class="py-12 text-center text-muted">No photos yet.</p>
    <?php endif ?>
  </div>
<?php endsnippet() ?>
