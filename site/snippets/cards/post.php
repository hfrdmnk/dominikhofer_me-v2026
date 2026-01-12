<?php
/**
 * Post card for feed
 * @param Kirby\Cms\Page $item - The post page
 */
$cover = $item->cover()->toFile();
?>
<article class="group">
  <a href="<?= $item->url() ?>" class="block">
    <?php snippet('author-row', ['item' => $item, 'showReadTime' => true]) ?>

    <h2 class="mt-3 text-lg font-semibold text-(--text-primary) group-hover:text-(--accent)">
      <?= $item->title() ?>
    </h2>

    <?php if ($item->excerpt()->isNotEmpty()): ?>
    <p class="mt-2 text-sm leading-relaxed text-(--text-secondary)">
      <?= $item->excerpt() ?>
    </p>
    <?php endif ?>

    <?php if ($cover): ?>
    <div class="mt-4 overflow-hidden rounded-(--radius-medium)">
      <img
        src="<?= $cover->resize(800)->url() ?>"
        alt=""
        class="aspect-video w-full object-cover transition-transform duration-300 group-hover:scale-105"
        loading="lazy"
      >
    </div>
    <?php endif ?>
  </a>
</article>
