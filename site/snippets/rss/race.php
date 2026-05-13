<?php
/**
 * RSS content for races
 * @var Kirby\Cms\Page $item
 * @var Kirby\Cms\Site $site
 * @var string $authorEmail
 * @var string $authorName
 * @var bool $showSubfeedHint
 */
?>
<table style="width: 100%; border-collapse: collapse; margin-bottom: 1em;">
  <tr>
    <td style="padding: 0.5em; text-align: center; border: 1px solid #ccc;">
      <strong><?= $item->distance()->value() ?></strong><br>
      <small>km</small>
    </td>
    <td style="padding: 0.5em; text-align: center; border: 1px solid #ccc;">
      <strong><?= $item->time()->value() ?></strong><br>
      <small>time</small>
    </td>
    <td style="padding: 0.5em; text-align: center; border: 1px solid #ccc;">
      <strong><?= $item->pace()->value() ?></strong><br>
      <small>min/km</small>
    </td>
  </tr>
</table>

<?php if ($item->location()->isNotEmpty()): ?>
<p><strong><?= htmlspecialchars($item->location()->value(), ENT_QUOTES, 'UTF-8') ?></strong></p>
<?php endif ?>

<?php if ($item->body()->isNotEmpty()): ?>
<?= $site->absoluteUrls($item->body()->kirbytext()) ?>
<?php endif ?>

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
