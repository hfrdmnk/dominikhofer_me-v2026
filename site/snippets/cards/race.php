<?php
/**
 * Race card for feed
 * @param Kirby\Cms\Page $item - The race page
 */
?>
<article class="group">
  <a href="<?= $item->url() ?>" class="block">
    <?php snippet('author-row', ['item' => $item]) ?>

    <h2 class="mt-3 text-lg font-semibold text-(--text-primary) group-hover:text-(--accent)">
      <?= $item->title() ?>
    </h2>

    <div class="mt-4 grid grid-cols-3 gap-3">
      <div class="rounded-(--radius-medium) bg-(--accent-bg) p-4 text-center">
        <span class="block font-mono text-2xl font-semibold text-(--text-primary)">
          <?= $item->distance() ?>
        </span>
        <span class="mt-1 block text-xs uppercase tracking-wide text-(--text-muted)">km</span>
      </div>

      <div class="rounded-(--radius-medium) bg-(--accent-bg) p-4 text-center">
        <span class="block font-mono text-2xl font-semibold text-(--text-primary)">
          <?= $item->time() ?>
        </span>
        <span class="mt-1 block text-xs uppercase tracking-wide text-(--text-muted)">time</span>
      </div>

      <div class="rounded-(--radius-medium) bg-(--accent-bg) p-4 text-center">
        <span class="block font-mono text-2xl font-semibold text-(--text-primary)">
          <?= $item->pace() ?>
        </span>
        <span class="mt-1 block text-xs uppercase tracking-wide text-(--text-muted)">min/km</span>
      </div>
    </div>
  </a>
</article>
