<?php
/**
 * Post card for feed
 * @param Kirby\Cms\Page $item - The post page
 */
$cover = $item->cover()->toFile();
$minutes = $item->body()->isNotEmpty() ? round(str_word_count(strip_tags($item->body()->kirbytext())) / 200) : 0;
$readTime = $minutes > 0 ? $minutes . ' min read' : null;
$detailUrl = $item->url();
?>
<article class="group grid grid-cols-[2rem_1fr] gap-x-3">
  <?php snippet('author-row', ['item' => $item]) ?>

  <a href="<?= $detailUrl ?>" class="col-start-2 mt-2 block">
    <h2 class="text-base font-medium text-primary hover:text-accent transition-colors">
      <?= $item->title() ?>
    </h2>
  </a>

  <?php if ($item->excerpt()->isNotEmpty()): ?>
  <p class="col-start-2 mt-2 text-sm leading-relaxed text-secondary">
    <?= $item->excerpt() ?>
  </p>
  <?php endif ?>

  <?php if ($cover): ?>
  <a href="<?= $detailUrl ?>" class="col-start-2 mt-3 block overflow-hidden rounded-medium">
    <img
      src="<?= $cover->resize(800)->url() ?>"
      alt=""
      class="aspect-video w-full object-cover"
      loading="lazy"
    >
  </a>
  <?php endif ?>

  <div class="col-start-2">
    <?php snippet('card-footer', [
      'item' => $item,
      'leftContent' => $readTime,
      'leftClass' => 'font-mono text-muted'
    ]) ?>
  </div>
</article>
