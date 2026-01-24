<?php
/**
 * Photos listing template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$feed = $page->children()
  ->listed()
  ->sortBy('date', 'desc')
  ->paginate(50);
?>
<?php snippet('layouts/base', ['pagination' => $feed->pagination()], slots: true) ?>
  <div class="grid grid-cols-[repeat(auto-fill,_minmax(150px,_1fr))] gap-0.5 p-4">
    <?php foreach ($feed as $photo): ?>
      <?php if ($image = $photo->image()): ?>
        <a href="<?= $photo->url() ?>" class="aspect-square overflow-hidden rounded-sm">
          <img
            src="<?= $image->resize(400)->url() ?>"
            alt="<?= $image->alt()->or($photo->title()) ?>"
            class="w-full h-full object-cover hover:opacity-80 transition-opacity"
            loading="lazy"
          >
        </a>
      <?php endif ?>
    <?php endforeach ?>
  </div>
<?php endsnippet() ?>
