<?php
/**
 * Race card for feed
 * @param Kirby\Cms\Page $item - The race page
 */
?>
<article class="group">
  <?php snippet('author-row', ['item' => $item]) ?>

  <a href="<?= $item->url() ?>" class="mt-3 block">
    <h2 class="text-base font-semibold text-primary group-hover:text-accent">
      <?= $item->title() ?>
    </h2>
  </a>

  <a href="<?= $item->url() ?>" class="mt-4 grid grid-cols-3 gap-3">
    <div class="rounded-medium bg-accent p-4 text-center">
      <span class="block font-mono text-xl font-semibold text-white">
        <?= $item->distance() ?>
      </span>
      <span class="mt-1 block text-base text-white/80">km</span>
    </div>

    <div class="rounded-medium bg-accent p-4 text-center">
      <span class="block font-mono text-xl font-semibold text-white">
        <?= $item->time() ?>
      </span>
      <span class="mt-1 block text-base text-white/80">time</span>
    </div>

    <div class="rounded-medium bg-accent p-4 text-center">
      <span class="block font-mono text-xl font-semibold text-white">
        <?= $item->pace() ?>
      </span>
      <span class="mt-1 block text-base text-white/80">min/km</span>
    </div>
  </a>

  <?php snippet('card-footer', ['item' => $item]) ?>
</article>
