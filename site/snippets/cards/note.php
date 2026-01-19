<?php
/**
 * Note card for feed
 * @param Kirby\Cms\Page $item - The note page
 */

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
      // Determine if video (HLS playlist) or image
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
$mediaCount = $hasLocalMedia ? count($mediaFiles) : count($remoteMedia);
$detailUrl = $item->url();
?>
<article class="group grid grid-cols-[2rem_1fr] gap-x-3">
  <?php snippet('author-row', ['item' => $item, 'linkUrl' => $detailUrl]) ?>

  <div class="col-start-2 prose prose-neutral prose-card mt-2 max-w-none leading-relaxed text-secondary">
    <?= $item->body()->kt() ?>
  </div>

  <?php if ($hasLocalMedia): ?>
  <a href="<?= $detailUrl ?>" class="col-start-2 mt-3 block overflow-hidden rounded-medium">
    <?php if ($mediaCount === 1): ?>
      <?php $file = $mediaFiles[0]; ?>
      <?php if ($file->type() === 'video'): ?>
      <video
        src="<?= $file->url() ?>"
        class="aspect-video w-full object-cover"
        muted
        loop
        autoplay
        playsinline
      ></video>
      <?php else: ?>
      <img
        src="<?= $file->resize(800)->url() ?>"
        alt=""
        class="aspect-[4/3] w-full object-cover"
        loading="lazy"
      >
      <?php endif ?>

    <?php elseif ($mediaCount === 2): ?>
      <div class="grid grid-cols-2 gap-1">
        <?php foreach ($mediaFiles as $file): ?>
          <?php if ($file->type() === 'video'): ?>
          <video
            src="<?= $file->url() ?>"
            class="aspect-square w-full object-cover"
            muted
            loop
            autoplay
            playsinline
          ></video>
          <?php else: ?>
          <img
            src="<?= $file->resize(400)->url() ?>"
            alt=""
            class="aspect-square w-full object-cover"
            loading="lazy"
          >
          <?php endif ?>
        <?php endforeach ?>
      </div>

    <?php elseif ($mediaCount === 3): ?>
      <div class="grid grid-cols-2 grid-rows-2 gap-1">
        <?php foreach ($mediaFiles as $index => $file): ?>
          <?php
            $spanClass = $index === 0 ? 'row-span-2' : '';
            $aspectClass = $index === 0 ? 'aspect-[2/3]' : 'aspect-square';
          ?>
          <?php if ($file->type() === 'video'): ?>
          <video
            src="<?= $file->url() ?>"
            class="<?= $spanClass ?> <?= $aspectClass ?> h-full w-full object-cover"
            muted
            loop
            autoplay
            playsinline
          ></video>
          <?php else: ?>
          <img
            src="<?= $file->resize(400)->url() ?>"
            alt=""
            class="<?= $spanClass ?> <?= $aspectClass ?> h-full w-full object-cover"
            loading="lazy"
          >
          <?php endif ?>
        <?php endforeach ?>
      </div>

    <?php elseif ($mediaCount === 4): ?>
      <div class="grid grid-cols-2 grid-rows-2 gap-1">
        <?php foreach ($mediaFiles as $file): ?>
          <?php if ($file->type() === 'video'): ?>
          <video
            src="<?= $file->url() ?>"
            class="aspect-square w-full object-cover"
            muted
            loop
            autoplay
            playsinline
          ></video>
          <?php else: ?>
          <img
            src="<?= $file->resize(400)->url() ?>"
            alt=""
            class="aspect-square w-full object-cover"
            loading="lazy"
          >
          <?php endif ?>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </a>

  <?php elseif ($hasRemoteMedia): ?>
  <a href="<?= $detailUrl ?>" class="col-start-2 mt-3 block overflow-hidden rounded-medium">
    <?php if ($mediaCount === 1): ?>
      <?php $media = $remoteMedia[0]; ?>
      <?php if ($media['type'] === 'video'): ?>
      <video
        src="<?= $media['url'] ?>"
        class="aspect-video w-full object-cover"
        muted
        loop
        autoplay
        playsinline
      ></video>
      <?php else: ?>
      <img
        src="<?= $media['url'] ?>"
        alt=""
        class="aspect-[4/3] w-full object-cover"
        loading="lazy"
      >
      <?php endif ?>

    <?php elseif ($mediaCount === 2): ?>
      <div class="grid grid-cols-2 gap-1">
        <?php foreach ($remoteMedia as $media): ?>
          <?php if ($media['type'] === 'video'): ?>
          <video
            src="<?= $media['url'] ?>"
            class="aspect-square w-full object-cover"
            muted
            loop
            autoplay
            playsinline
          ></video>
          <?php else: ?>
          <img
            src="<?= $media['url'] ?>"
            alt=""
            class="aspect-square w-full object-cover"
            loading="lazy"
          >
          <?php endif ?>
        <?php endforeach ?>
      </div>

    <?php elseif ($mediaCount === 3): ?>
      <div class="grid grid-cols-2 grid-rows-2 gap-1">
        <?php foreach ($remoteMedia as $index => $media): ?>
          <?php
            $spanClass = $index === 0 ? 'row-span-2' : '';
            $aspectClass = $index === 0 ? 'aspect-[2/3]' : 'aspect-square';
          ?>
          <?php if ($media['type'] === 'video'): ?>
          <video
            src="<?= $media['url'] ?>"
            class="<?= $spanClass ?> <?= $aspectClass ?> h-full w-full object-cover"
            muted
            loop
            autoplay
            playsinline
          ></video>
          <?php else: ?>
          <img
            src="<?= $media['url'] ?>"
            alt=""
            class="<?= $spanClass ?> <?= $aspectClass ?> h-full w-full object-cover"
            loading="lazy"
          >
          <?php endif ?>
        <?php endforeach ?>
      </div>

    <?php else: ?>
      <div class="grid grid-cols-2 grid-rows-2 gap-1">
        <?php foreach (array_slice($remoteMedia, 0, 4) as $media): ?>
          <?php if ($media['type'] === 'video'): ?>
          <video
            src="<?= $media['url'] ?>"
            class="aspect-square w-full object-cover"
            muted
            loop
            autoplay
            playsinline
          ></video>
          <?php else: ?>
          <img
            src="<?= $media['url'] ?>"
            alt=""
            class="aspect-square w-full object-cover"
            loading="lazy"
          >
          <?php endif ?>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </a>
  <?php endif ?>

  <div class="col-start-2">
    <?php snippet('card-footer', ['item' => $item]) ?>
  </div>
</article>
