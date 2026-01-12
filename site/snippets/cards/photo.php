<?php
/**
 * Photo card for feed
 * @param Kirby\Cms\Page $item - The photo page
 */
$image = $item->files()->first();
?>
<article class="group">
  <a href="<?= $item->url() ?>" class="block">
    <?php snippet('author-row', ['item' => $item]) ?>

    <?php if ($image): ?>
    <div class="mt-4 overflow-hidden rounded-(--radius-medium)">
      <img
        src="<?= $image->resize(800)->url() ?>"
        alt=""
        class="w-full object-cover transition-transform duration-300 group-hover:scale-105"
        loading="lazy"
      >
    </div>
    <?php endif ?>

    <div class="mt-3 flex items-center gap-4 text-sm text-(--text-muted)">
      <?php if ($item->location()->isNotEmpty()): ?>
      <span><?= $item->location() ?></span>
      <?php endif ?>
    </div>
  </a>
</article>
