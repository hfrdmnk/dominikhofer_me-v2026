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
  <?php snippet('author-row', ['item' => $item, 'showTags' => false]) ?>

  <a href="<?= $detailUrl ?>" class="col-span-2 md:col-start-2 md:col-span-1 mt-2 block">
    <h2 class="text-base font-medium text-primary hover:text-accent transition-colors">
      <?= $item->title() ?>
    </h2>
  </a>

  <?php if ($item->excerpt()->isNotEmpty()): ?>
  <p class="col-span-2 md:col-start-2 md:col-span-1 mt-2 text-sm leading-relaxed text-secondary">
    <?= $item->excerpt() ?>
  </p>
  <?php endif ?>

  <?php if ($cover): ?>
  <a href="<?= $detailUrl ?>" class="col-span-2 md:col-start-2 md:col-span-1 mt-3 block overflow-hidden rounded-medium">
    <img
      src="<?= $cover->resize(800)->url() ?>"
      alt=""
      class="aspect-video w-full object-cover"
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
      'leftContent' => $readTime,
      'leftClass' => 'font-mono text-muted'
    ]) ?>
  </div>
</article>
