<?php
/**
 * Default template for static pages (about, now, slash)
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="mx-auto max-w-container-prose px-4 py-8">
    <?php snippet('author-row', ['item' => $page]) ?>

    <?php if ($page->text()->isNotEmpty()): ?>
    <div class="prose prose-neutral mt-8 max-w-none">
      <?= $page->text()->kt() ?>
    </div>
    <?php endif ?>
  </article>
<?php endsnippet() ?>
