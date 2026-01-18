<?php
/**
 * Single race template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <?php snippet('author-row', ['item' => $page, 'relativeDate' => false]) ?>

    <div class="mt-6 grid grid-cols-3 gap-3">
      <div class="rounded-medium bg-accent-bg p-6 text-center">
        <span class="block font-mono text-3xl font-semibold text-primary">
          <?= $page->distance() ?>
        </span>
        <span class="mt-2 block text-sm uppercase tracking-wide text-muted">km</span>
      </div>

      <div class="rounded-medium bg-accent-bg p-6 text-center">
        <span class="block font-mono text-3xl font-semibold text-primary">
          <?= $page->time() ?>
        </span>
        <span class="mt-2 block text-sm uppercase tracking-wide text-muted">time</span>
      </div>

      <div class="rounded-medium bg-accent-bg p-6 text-center">
        <span class="block font-mono text-3xl font-semibold text-primary">
          <?= $page->pace() ?>
        </span>
        <span class="mt-2 block text-sm uppercase tracking-wide text-muted">min/km</span>
      </div>
    </div>

    <?php if ($page->location()->isNotEmpty()): ?>
    <p class="mt-6 text-sm text-muted">
      <?= $page->location() ?>
    </p>
    <?php endif ?>

    <?php if ($page->body()->isNotEmpty()): ?>
    <div class="prose prose-neutral mt-8 max-w-none">
      <?= $page->body()->kirbytext() ?>
    </div>
    <?php endif ?>

    <?php if ($page->tags()->isNotEmpty()): ?>
    <div class="mt-8 flex flex-wrap gap-2">
      <?php foreach ($page->tags()->split() as $tag): ?>
      <span class="rounded-full bg-accent-bg px-3 py-1 text-sm text-accent">
        <?= $tag ?>
      </span>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </article>
<?php endsnippet() ?>
