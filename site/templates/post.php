<?php
/**
 * Single post template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$cover = $page->cover()->toFile();
$minutes = $page->body()->isNotEmpty() ? round(str_word_count(strip_tags($page->body()->kirbytext())) / 200) : 0;
$readTime = $minutes > 0 ? $minutes . ' min read' : null;
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <div class="grid grid-cols-[2rem_1fr] gap-x-3">
      <?php snippet('author-row', ['item' => $page, 'metadata' => $readTime]) ?>
    </div>

    <div class="mt-6 space-y-6">
      <?php if ($cover): ?>
      <div class="overflow-hidden rounded-medium">
        <img
          src="<?= $cover->resize(1200)->url() ?>"
          alt=""
          class="w-full"
        >
      </div>
      <?php endif ?>

      <div class="prose prose-neutral prose-headings:font-medium prose-strong:font-medium prose-img:rounded-small max-w-none">
        <?= $page->body()->kirbytext() ?>
      </div>
    </div>

    <?php snippet('card-footer', ['item' => $page, 'tags' => $page->tags()->split()]) ?>
  </article>
<?php endsnippet() ?>
