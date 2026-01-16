<?php
/**
 * Note card for feed
 * @param Kirby\Cms\Page $item - The note page
 */
$image = $item->files()->first();
?>
<article class="group">
  <a href="<?= $item->url() ?>" class="block">
    <?php snippet('author-row', ['item' => $item]) ?>

    <div class="mt-3 text-sm leading-relaxed text-secondary">
      <?= $item->body()->kt() ?>
    </div>

    <?php if ($image): ?>
    <div class="mt-4 overflow-hidden rounded-medium">
      <img
        src="<?= $image->resize(800)->url() ?>"
        alt=""
        class="w-full object-cover transition-transform duration-300 group-hover:scale-105"
        loading="lazy"
      >
    </div>
    <?php endif ?>
  </a>
</article>
