<?php
/**
 * Race card for feed
 * @param Kirby\Cms\Page $item - The race page
 */
$detailUrl = $item->url();
$tags = $item->tags()->isNotEmpty() ? $item->tags()->split() : [];
?>
<article class="group grid grid-cols-[2rem_1fr] gap-x-3">
  <?php snippet('author-row', ['item' => $item]) ?>

  <a href="<?= $detailUrl ?>" class="col-span-2 md:col-start-2 md:col-span-1 mt-2 block">
    <h2 class="text-base font-medium text-primary hover:text-accent transition-colors">
      <?= $item->title() ?>
    </h2>
  </a>

  <a href="<?= $detailUrl ?>" class="col-span-2 md:col-start-2 md:col-span-1 mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
    <div class="rounded-small border border-accent bg-accent/20 p-4 text-center">
      <span class="block font-mono text-xl font-medium text-accent">
        <?= $item->distance() ?>
      </span>
      <span class="mt-1 block text-base text-accent/80">km</span>
    </div>

    <div class="rounded-small border border-accent bg-accent/20 p-4 text-center">
      <span class="block font-mono text-xl font-medium text-accent">
        <?= $item->time() ?>
      </span>
      <span class="mt-1 block text-base text-accent/80">time</span>
    </div>

    <div class="rounded-small border border-accent bg-accent/20 p-4 text-center">
      <span class="block font-mono text-xl font-medium text-accent">
        <?= $item->pace() ?>
      </span>
      <span class="mt-1 block text-base text-accent/80">min/km</span>
    </div>
  </a>

  <div class="col-span-2 md:col-start-2 md:col-span-1">
    <?php snippet('card-footer', ['item' => $item, 'tags' => $tags]) ?>
  </div>
</article>
