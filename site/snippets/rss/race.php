<?php
/**
 * RSS content for races
 * @var Kirby\Cms\Page $item
 * @var Kirby\Cms\Site $site
 * @var string $authorEmail
 * @var string $authorName
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
