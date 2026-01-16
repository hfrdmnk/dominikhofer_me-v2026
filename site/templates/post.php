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
    <?php snippet('author-row', ['item' => $page, 'showReadTime' => true]) ?>

    <?php if ($cover): ?>
    <div class="mt-6 overflow-hidden rounded-medium">
      <img
        src="<?= $cover->resize(1200)->url() ?>"
        alt=""
        class="w-full"
      >
    </div>
    <?php endif ?>

    <div class="prose prose-neutral mt-8 max-w-none">
      <?= $page->body()->kirbytext() ?>
    </div>

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
