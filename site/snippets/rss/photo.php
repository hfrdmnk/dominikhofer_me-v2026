<?php
/**
 * RSS content for photos
 * @var Kirby\Cms\Page $item
 * @var Kirby\Cms\Site $site
 * @var string $authorEmail
 * @var string $authorName
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
