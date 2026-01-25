<?php
/**
 * Photo card for feed
 * @param Kirby\Cms\Page $item - The photo page
 */
$image = $item->files()->first();
$location = $item->location()->isNotEmpty() ? $item->location()->value() : null;
$detailUrl = $item->url();
$tags = $item->tags()->isNotEmpty() ? $item->tags()->split() : [];
?>
<article class="group grid grid-cols-[2rem_1fr] gap-x-3">
  <?php snippet('author-row', ['item' => $item, 'metadata' => $location]) ?>

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

  <div class="col-span-2 md:col-start-2 md:col-span-1">
    <?php snippet('card-footer', ['item' => $item, 'tags' => $tags]) ?>
  </div>
</article>
