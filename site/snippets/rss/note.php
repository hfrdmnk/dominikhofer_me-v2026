<?php
/**
 * RSS content for notes
 * @var Kirby\Cms\Page $item
 * @var Kirby\Cms\Site $site
 * @var string $authorEmail
 * @var string $authorName
 */

// Check if this is a Bluesky virtual page
$isBluesky = str_starts_with($item->content()->get('uuid')->value() ?? '', 'bluesky://');

// Check if this is a thread (body contains separator)
$bodyContent = $item->body()->value();
$bodyParts = explode("\n\n---\n\n", $bodyContent);

// Collect local media files
$mediaFiles = [];
for ($i = 1; $i <= 4; $i++) {
  $file = $item->{'media_' . $i}()->toFile();
  if ($file) {
    $mediaFiles[] = $file;
  }
}

// Check for remote media URLs (from Bluesky virtual pages)
$remoteMedia = [];
if (empty($mediaFiles) && $item->media_urls()->isNotEmpty()) {
  $urls = array_filter(array_map('trim', explode(',', $item->media_urls()->value())));
  foreach ($urls as $url) {
    if (!empty($url)) {
      $isVideo = str_contains($url, 'video.bsky.app') || str_ends_with($url, '.m3u8');
      $remoteMedia[] = [
        'url' => $url,
        'type' => $isVideo ? 'video' : 'image'
      ];
    }
  }
}
?>
<?php foreach ($bodyParts as $index => $part): ?>
  <?php if ($index > 0): ?>
<hr style="margin: 1.5em 0; border: none; border-top: 1px solid #ccc;">
  <?php endif ?>
  <?php if ($isBluesky): ?>
<?= kirbytext(BlueskyParser::escapeMarkdownHeadings($part)) ?>
  <?php else: ?>
<?= kirbytext($part) ?>
  <?php endif ?>
<?php endforeach ?>

<?php if ($item->quoted_post()->isNotEmpty()): ?>
  <?php $quotedPost = json_decode($item->quoted_post()->value(), true); ?>
  <?php if ($quotedPost): ?>
    <?php
      // Convert AT URI to Bluesky web URL
      $atUri = $quotedPost['uri'] ?? '';
      $quotedPostUrl = '';
      if (preg_match('/at:\/\/([^\/]+)\/app\.bsky\.feed\.post\/(.+)/', $atUri, $matches)) {
        $quotedPostUrl = 'https://bsky.app/profile/' . $matches[1] . '/post/' . $matches[2];
      }
    ?>
<blockquote style="margin: 1em 0; padding: 1em; background: #f5f5f5; border-left: 3px solid #ccc;">
  <p style="margin: 0 0 0.5em;">
    <strong><?= htmlspecialchars($quotedPost['author_name'] ?: $quotedPost['author_handle'], ENT_QUOTES, 'UTF-8') ?></strong>
    <small>@<?= htmlspecialchars($quotedPost['author_handle'], ENT_QUOTES, 'UTF-8') ?></small>
  </p>
  <p style="margin: 0;"><?= nl2br(htmlspecialchars($quotedPost['text'], ENT_QUOTES, 'UTF-8')) ?></p>
  <?php if ($quotedPostUrl): ?>
  <p style="margin: 0.5em 0 0;"><a href="<?= $quotedPostUrl ?>">View on Bluesky</a></p>
  <?php endif ?>
</blockquote>
  <?php endif ?>
<?php endif ?>

<?php if ($item->external_link()->isNotEmpty()): ?>
  <?php $link = json_decode($item->external_link()->value(), true); ?>
  <?php if ($link): ?>
<p style="margin: 1em 0;">
  <a href="<?= htmlspecialchars($link['uri'], ENT_QUOTES, 'UTF-8') ?>" style="display: block; padding: 1em; border: 1px solid #ccc; text-decoration: none; color: inherit;">
    <?php if (!empty($link['thumb'])): ?>
    <img src="<?= htmlspecialchars($link['thumb'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-width: 100%; height: auto; margin-bottom: 0.5em;">
    <?php endif ?>
    <strong><?= htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8') ?></strong>
    <?php if (!empty($link['description'])): ?>
    <br><small><?= htmlspecialchars($link['description'], ENT_QUOTES, 'UTF-8') ?></small>
    <?php endif ?>
    <br><small><?= parse_url($link['uri'], PHP_URL_HOST) ?></small>
  </a>
</p>
  <?php endif ?>
<?php endif ?>

<?php if (count($mediaFiles) > 0): ?>
  <?php foreach ($mediaFiles as $file): ?>
    <?php if ($file->type() === 'video'): ?>
<p><video src="<?= $file->url() ?>" controls style="max-width: 100%;"></video></p>
    <?php else: ?>
<p><img src="<?= $file->url() ?>" alt="" style="max-width: 100%; height: auto;"></p>
    <?php endif ?>
  <?php endforeach ?>
<?php elseif (count($remoteMedia) > 0): ?>
  <?php foreach ($remoteMedia as $media): ?>
    <?php if ($media['type'] === 'video'): ?>
<p><video src="<?= htmlspecialchars($media['url'], ENT_QUOTES, 'UTF-8') ?>" controls style="max-width: 100%;"></video></p>
    <?php else: ?>
<p><img src="<?= htmlspecialchars($media['url'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-width: 100%; height: auto;"></p>
    <?php endif ?>
  <?php endforeach ?>
<?php endif ?>

<hr>
<p>
  <a href="<?= $item->url() ?>">View on site</a> |
  <a href="mailto:<?= $authorEmail ?>?subject=Re: Note">Reply via email</a>
</p>
