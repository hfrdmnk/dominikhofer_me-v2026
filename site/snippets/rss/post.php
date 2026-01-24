<?php
/**
 * RSS content for posts
 * @var Kirby\Cms\Page $item
 * @var Kirby\Cms\Site $site
 * @var string $authorEmail
 * @var string $authorName
 */

$cover = $item->cover()->toFile();
?>
<?php if ($cover): ?>
<p><img src="<?= $cover->url() ?>" alt="" style="max-width: 100%; height: auto;"></p>
<?php endif ?>

<?= $item->body()->kirbytext() ?>

<hr>
<p>
  <a href="<?= $item->url() ?>">View on site</a> |
  <a href="mailto:<?= $authorEmail ?>?subject=Re: <?= htmlspecialchars($item->title()->value(), ENT_QUOTES, 'UTF-8') ?>">Reply via email</a>
</p>
