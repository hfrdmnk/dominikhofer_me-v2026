<?php
/**
 * Note card for feed
 * @param Kirby\Cms\Page $item - The note page
 */
$image = $item->files()->first();
?>
<article class="group">
  <?php snippet('author-row', ['item' => $item, 'linkUrl' => $item->url()]) ?>

  <div class="prose prose-neutral mt-3 max-w-none text-sm leading-relaxed text-secondary">
    <?= $item->body()->kt() ?>
  </div>

  <?php if ($image): ?>
  <a href="<?= $item->url() ?>" class="mt-4 block overflow-hidden rounded-medium">
    <img
      src="<?= $image->resize(800)->url() ?>"
      alt=""
      class="w-full object-cover transition-transform duration-300 group-hover:scale-105"
      loading="lazy"
    >
  </a>
  <?php endif ?>

  <?php snippet('card-footer', ['item' => $item]) ?>
</article>
