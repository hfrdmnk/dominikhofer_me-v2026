<?php
/**
 * Single photo template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$image = $page->files()->first();
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <?php snippet('author-row', ['item' => $page]) ?>

    <?php if ($image): ?>
    <div class="mt-6 overflow-hidden rounded-(--radius-medium)">
      <img
        src="<?= $image->resize(1200)->url() ?>"
        alt=""
        class="w-full"
      >
    </div>

    <div class="mt-4 flex items-center justify-between text-sm text-(--text-muted)">
      <div class="flex items-center gap-4">
        <?php if ($page->location()->isNotEmpty()): ?>
        <span><?= $page->location() ?></span>
        <?php endif ?>
      </div>
    </div>
    <?php endif ?>

    <?php if ($page->content()->isNotEmpty()): ?>
    <div class="prose prose-neutral mt-6 max-w-none">
      <?= $page->content()->kt() ?>
    </div>
    <?php endif ?>

    <?php if ($page->tags()->isNotEmpty()): ?>
    <div class="mt-8 flex flex-wrap gap-2">
      <?php foreach ($page->tags()->split() as $tag): ?>
      <span class="rounded-full bg-(--accent-bg) px-3 py-1 text-sm text-(--accent)">
        <?= $tag ?>
      </span>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </article>
<?php endsnippet() ?>
