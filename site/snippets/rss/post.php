<?php
/**
 * RSS content for posts
 * @var Kirby\Cms\Page $item
 * @var Kirby\Cms\Site $site
 * @var string $authorEmail
 * @var string $authorName
 * @var bool $showSubfeedHint
 */

$cover = $item->cover()->toFile();
?>
<?php if ($cover): ?>
<p><img src="<?= $cover->url() ?>" alt="" style="max-width: 100%; height: auto;"></p>
<?php endif ?>

<?= $site->absoluteUrls($item->body()->kirbytext()) ?>

<hr>
<p>
  <a href="<?= $item->url() ?>">View on site</a> |
  <a href="mailto:<?= $authorEmail ?>?subject=Re: <?= htmlspecialchars($item->title()->value(), ENT_QUOTES, 'UTF-8') ?>">Reply via email</a>
</p>
<?php if ($showSubfeedHint ?? false): ?>
<p style="font-size: 0.9em; color: #666;">
  Too much for one feed? Subscribe to just
  <a href="<?= $site->url() ?>/posts/rss">Posts</a>,
  <a href="<?= $site->url() ?>/notes/rss">Notes</a>,
  <a href="<?= $site->url() ?>/photos/rss">Photos</a>, or
  <a href="<?= $site->url() ?>/races/rss">Races</a>.
</p>
<?php endif ?>
