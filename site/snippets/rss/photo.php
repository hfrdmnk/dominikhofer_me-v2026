<?php
/**
 * RSS content for photos
 * @var Kirby\Cms\Page $item
 * @var Kirby\Cms\Site $site
 * @var string $authorEmail
 * @var string $authorName
 * @var bool $showSubfeedHint
 */

$image = $item->files()->first();
?>
<?php if ($image): ?>
<p><img src="<?= $image->url() ?>" alt="" style="max-width: 100%; height: auto;"></p>
<?php endif ?>

<?php if ($item->location()->isNotEmpty()): ?>
<p><strong><?= htmlspecialchars($item->location()->value(), ENT_QUOTES, 'UTF-8') ?></strong></p>
<?php endif ?>

<?php if ($item->body()->isNotEmpty()): ?>
<?= $site->absoluteUrls($item->body()->kirbytext()) ?>
<?php endif ?>

<hr>
<p>
  <a href="<?= $item->url() ?>">View on site</a> |
  <a href="mailto:<?= $authorEmail ?>?subject=Re: Photo">Reply via email</a>
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
