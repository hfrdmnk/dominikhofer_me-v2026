<?php
/**
 * Single post template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$cover = $page->cover()->toFile();
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <div class="grid grid-cols-[2rem_1fr] gap-x-3">
      <?php snippet('author-row', ['item' => $page, 'showReadTime' => true, 'relativeDate' => false]) ?>
    </div>

    <?php if ($cover): ?>
    <div class="mt-6 overflow-hidden rounded-medium">
      <img
        src="<?= $cover->resize(1200)->url() ?>"
        alt=""
        class="w-full"
      >
    </div>
    <?php endif ?>

    <div class="prose prose-neutral prose-headings:font-medium prose-strong:font-medium prose-img:rounded-small mt-8 max-w-none">
      <?= $page->body()->kirbytext() ?>
    </div>

    <?php snippet('card-footer', ['item' => $page]) ?>

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
