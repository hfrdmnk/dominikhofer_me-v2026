<?php
/**
 * Tag archive template - Shows all content with a specific tag
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

$tag = $page->content()->get('tag')->value();

$feed = $site->index()
  ->listed()
  ->filterBy('intendedTemplate', 'in', ['post', 'note', 'photo', 'race'])
  ->filterBy('tags', $tag, ',')
  ->sortBy('date', 'desc')
  ->paginate(50);
?>
<?php snippet('layouts/base', ['header' => 'header-tag', 'pagination' => $feed->pagination()], slots: true) ?>
  <div class="px-4 py-8">
    <?php foreach ($feed as $item): ?>
      <?php snippet('feed-item', ['item' => $item]) ?>
    <?php endforeach ?>
  </div>
<?php endsnippet() ?>
