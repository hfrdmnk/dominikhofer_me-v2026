<?php
/**
 * Photo card for feed
 * @param Kirby\Cms\Page $item - The photo page
 */
$image = $item->files()->first();
$location = $item->location()->isNotEmpty() ? $item->location()->value() : null;
$detailUrl = $item->url();
?>
<article class="group grid grid-cols-[2rem_1fr] gap-x-3">
  <?php snippet('author-row', ['item' => $item, 'showTags' => false]) ?>

  <?php if ($image): ?>
  <a href="<?= $detailUrl ?>" class="col-span-2 md:col-start-2 md:col-span-1 mt-3 block overflow-hidden rounded-medium">
    <img
      src="<?= $image->resize(800)->url() ?>"
      alt=""
      class="w-full object-cover"
      loading="lazy"
    >
  </a>
  <?php endif ?>

  <?php $tags = $item->tags()->isNotEmpty() ? $item->tags()->split() : []; ?>
  <?php if (count($tags) > 0): ?>
  <div class="col-span-2 md:col-start-2 md:col-span-1 mt-3 flex flex-wrap gap-2 font-mono text-xs text-muted">
    <?php foreach ($tags as $tag): ?>
    <a href="<?= url('tag/' . urlencode($tag)) ?>" class="hover:text-accent transition-colors">#<?= htmlspecialchars($tag) ?></a>
    <?php endforeach ?>
  </div>
  <?php endif ?>

  <div class="col-span-2 md:col-start-2 md:col-span-1">
    <?php snippet('card-footer', [
      'item' => $item,
      'leftContent' => $location,
      'leftClass' => 'font-mono text-muted'
    ]) ?>
  </div>
</article>
