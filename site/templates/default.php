<?php
/**
 * Default template for static pages (about, now, slash)
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$cover = $page->cover()->toFile();
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <?php if ($page->updated()->isNotEmpty()): ?>
    <p class="font-mono text-xs text-muted">
      Updated <?= $site->timeAgoFromTimestamp($page->updated()->toDate()) ?>
    </p>
    <?php endif ?>

    <?php if ($cover): ?>
    <div class="mt-6 overflow-hidden rounded-medium">
      <img
        src="<?= $cover->resize(1200)->url() ?>"
        alt=""
        class="w-full"
      >
    </div>
    <?php endif ?>

    <?php if ($page->body()->isNotEmpty()): ?>
    <div class="prose prose-neutral prose-headings:font-medium prose-strong:font-medium prose-img:rounded-small mt-6 max-w-none">
      <?= $page->body()->toBlocks() ?>
    </div>
    <?php endif ?>
  </article>
<?php endsnippet() ?>
