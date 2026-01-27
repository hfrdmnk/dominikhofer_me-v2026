<?php
/**
 * Single photo template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$image = $page->files()->first();
$location = $page->location()->isNotEmpty() ? $page->location()->value() : null;
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <div class="grid grid-cols-[2rem_1fr] gap-x-3">
      <?php snippet('author-row', ['item' => $page, 'metadata' => $location]) ?>
    </div>

    <div class="mt-6 space-y-6">
      <?php if ($image): ?>
      <div class="overflow-hidden rounded-medium">
        <img
          src="<?= $image->resize(1200)->url() ?>"
          alt=""
          class="w-full"
        >
      </div>
      <?php endif ?>

      <?php if ($page->body()->isNotEmpty()): ?>
      <div class="prose prose-neutral max-w-none">
        <?= $page->body()->kt() ?>
      </div>
      <?php endif ?>
    </div>

    <?php snippet('card-footer', [
      'item' => $page,
      'tags' => $page->tags()->split()
    ]) ?>
  </article>
<?php endsnippet() ?>
