<?php
/**
 * Single note template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

// Collect local media files
$mediaFiles = [];
for ($i = 1; $i <= 4; $i++) {
  $file = $page->{'media_' . $i}()->toFile();
  if ($file) {
    $mediaFiles[] = $file;
  }
}

// Check for remote media URLs (from Bluesky virtual pages)
$remoteMedia = [];
if (empty($mediaFiles) && $page->media_urls()->isNotEmpty()) {
  $urls = array_filter(array_map('trim', explode(',', $page->media_urls()->value())));
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

$hasLocalMedia = count($mediaFiles) > 0;
$hasRemoteMedia = count($remoteMedia) > 0;
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <?php snippet('author-row', ['item' => $page, 'relativeDate' => false]) ?>

    <div class="prose prose-neutral prose-headings:font-medium prose-strong:font-medium prose-img:rounded-small mt-6 max-w-none">
      <?= $page->body()->kt() ?>
    </div>

    <?php if ($hasLocalMedia): ?>
    <div class="mt-6 flex flex-col gap-4">
      <?php foreach ($mediaFiles as $file): ?>
        <?php if ($file->type() === 'video'): ?>
        <video
          src="<?= $file->url() ?>"
          class="w-full rounded-medium"
          controls
          playsinline
        ></video>
        <?php else: ?>
        <img
          src="<?= $file->resize(1200)->url() ?>"
          alt=""
          class="w-full rounded-medium"
        >
        <?php endif ?>
      <?php endforeach ?>
    </div>
    <?php elseif ($hasRemoteMedia): ?>
    <div class="mt-6 flex flex-col gap-4">
      <?php foreach ($remoteMedia as $media): ?>
        <?php if ($media['type'] === 'video'): ?>
        <video
          src="<?= $media['url'] ?>"
          class="w-full rounded-medium"
          controls
          playsinline
        ></video>
        <?php else: ?>
        <img
          src="<?= $media['url'] ?>"
          alt=""
          class="w-full rounded-medium"
        >
        <?php endif ?>
      <?php endforeach ?>
    </div>
    <?php endif ?>

    <?php if ($page->tags()->isNotEmpty()): ?>
    <div class="mt-8 flex flex-wrap gap-2">
      <?php foreach ($page->tags()->split() as $tag): ?>
      <span class="rounded-full bg-accent-bg px-3 py-1 text-sm text-accent">
        <?= $tag ?>
      </span>
      <?php endforeach ?>
    </div>
    <?php endif ?>
  </article>
<?php endsnippet() ?>
