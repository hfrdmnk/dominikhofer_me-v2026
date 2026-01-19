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
  <?php snippet('author-row', ['item' => $item]) ?>

  <?php if ($image): ?>
  <a href="<?= $detailUrl ?>" class="col-start-2 mt-3 block overflow-hidden rounded-medium">
    <img
      src="<?= $image->resize(800)->url() ?>"
      alt=""
      class="w-full object-cover"
      loading="lazy"
    >
  </a>
  <?php endif ?>

  <div class="col-start-2">
    <?php snippet('card-footer', [
      'item' => $item,
      'leftContent' => $location,
      'leftClass' => 'font-mono text-muted'
    ]) ?>
  </div>
</article>
