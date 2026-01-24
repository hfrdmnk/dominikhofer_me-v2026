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
    <div class="grid grid-cols-[2rem_1fr] gap-x-3">
      <?php snippet('author-row', ['item' => $page, 'showTags' => false]) ?>
    </div>

    <?php if ($image): ?>
    <div class="mt-6 overflow-hidden rounded-medium">
      <img
        src="<?= $image->resize(1200)->url() ?>"
        alt=""
        class="w-full"
      >
    </div>

    <?php snippet('card-footer', [
      'item' => $page,
      'leftContent' => $page->location()->isNotEmpty() ? $page->location()->value() : null,
      'tags' => $page->tags()->split()
    ]) ?>
    <?php endif ?>

    <?php if ($page->body()->isNotEmpty()): ?>
    <div class="prose prose-neutral mt-6 max-w-none">
      <?= $page->body()->kt() ?>
    </div>
    <?php endif ?>
  </article>
<?php endsnippet() ?>
