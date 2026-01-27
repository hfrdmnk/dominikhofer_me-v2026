<?php
/**
 * Single race template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$location = $page->location()->isNotEmpty() ? $page->location()->value() : null;
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <div class="grid grid-cols-[2rem_1fr] gap-x-3">
      <?php snippet('author-row', ['item' => $page, 'metadata' => $location]) ?>
    </div>

    <div class="mt-6 space-y-6">
      <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
        <div class="rounded-small border border-accent bg-accent/20 p-4 text-center">
          <span class="block font-mono text-xl font-medium text-accent">
            <?= $page->distance() ?>
          </span>
          <span class="mt-1 block text-base text-accent/80">km</span>
        </div>

        <div class="rounded-small border border-accent bg-accent/20 p-4 text-center">
          <span class="block font-mono text-xl font-medium text-accent">
            <?= $page->time() ?>
          </span>
          <span class="mt-1 block text-base text-accent/80">time</span>
        </div>

        <div class="rounded-small border border-accent bg-accent/20 p-4 text-center">
          <span class="block font-mono text-xl font-medium text-accent">
            <?= $page->pace() ?>
          </span>
          <span class="mt-1 block text-base text-accent/80">min/km</span>
        </div>
      </div>

      <?php if ($page->body()->isNotEmpty()): ?>
      <div class="prose prose-neutral max-w-none">
        <?= $page->body()->kirbytext() ?>
      </div>
      <?php endif ?>
    </div>

    <?php snippet('card-footer', [
      'item' => $page,
      'tags' => $page->tags()->split()
    ]) ?>
  </article>
<?php endsnippet() ?>
