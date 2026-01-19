<?php
/**
 * Race card for feed
 * @param Kirby\Cms\Page $item - The race page
 */
$detailUrl = $item->url();
?>
<article class="group grid grid-cols-[2rem_1fr] gap-x-3">
  <?php snippet('author-row', ['item' => $item]) ?>

  <a href="<?= $detailUrl ?>" class="col-start-2 mt-2 block">
    <h2 class="text-base font-medium text-primary hover:text-accent transition-colors">
      <?= $item->title() ?>
    </h2>
  </a>

  <a href="<?= $detailUrl ?>" class="col-start-2 mt-3 grid grid-cols-3 gap-3">
    <div class="rounded-medium bg-accent p-4 text-center">
      <span class="block font-mono text-xl font-medium text-white">
        <?= $item->distance() ?>
      </span>
      <span class="mt-1 block text-base text-white/80">km</span>
    </div>

    <div class="rounded-medium bg-accent p-4 text-center">
      <span class="block font-mono text-xl font-medium text-white">
        <?= $item->time() ?>
      </span>
      <span class="mt-1 block text-base text-white/80">time</span>
    </div>

    <div class="rounded-medium bg-accent p-4 text-center">
      <span class="block font-mono text-xl font-medium text-white">
        <?= $item->pace() ?>
      </span>
      <span class="mt-1 block text-base text-white/80">min/km</span>
    </div>
  </a>

  <div class="col-start-2">
    <?php snippet('card-footer', ['item' => $item]) ?>
  </div>
</article>
