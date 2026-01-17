<?php
/**
 * Photo card for feed
 * @param Kirby\Cms\Page $item - The photo page
 */
$image = $item->files()->first();
$location = $item->location()->isNotEmpty() ? $item->location()->value() : null;
?>
<article class="group">
  <?php snippet('author-row', ['item' => $item]) ?>

  <?php if ($image): ?>
  <a href="<?= $item->url() ?>" class="mt-4 block overflow-hidden rounded-medium">
    <img
      src="<?= $image->resize(800)->url() ?>"
      alt=""
      class="w-full object-cover"
      loading="lazy"
    >
  </a>
  <?php endif ?>

  <?php snippet('card-footer', [
    'item' => $item,
    'leftContent' => $location,
    'leftClass' => 'font-mono text-muted'
  ]) ?>
</article>
