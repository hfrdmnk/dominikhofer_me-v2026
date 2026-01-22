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
<?php snippet('layouts/base', ['pagination' => $feed->pagination()], slots: true) ?>
  <div class="px-4 py-8">
    <?php if ($feed->isNotEmpty()): ?>
      <?php foreach ($feed as $item): ?>
        <?php snippet('feed-item', ['item' => $item]) ?>
      <?php endforeach ?>
    <?php else: ?>
      <p class="py-12 text-center text-muted">No photos yet.</p>
    <?php endif ?>
  </div>
<?php endsnippet() ?>
