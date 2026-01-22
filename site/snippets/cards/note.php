<?php
/**
 * Note card for feed
 * @param Kirby\Cms\Page $item - The note page
 */

// Check if this is a thread (body contains separator)
$bodyContent = $item->body()->value();
$bodyParts = explode("\n\n---\n\n", $bodyContent);
$firstPost = $bodyParts[0];
$hasThread = count($bodyParts) > 1;
$threadCount = $item->thread_count()->or(count($bodyParts))->value();

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

  <?php $isBluesky = str_starts_with($item->content()->get('uuid')->value() ?? '', 'bluesky://'); ?>
  <div class="col-start-2 prose prose-neutral prose-card mt-2 max-w-none leading-relaxed text-secondary">
    <?= $isBluesky ? kirbytext(BlueskyParser::escapeMarkdownHeadings($firstPost)) : kirbytext($firstPost) ?>
    <?php if ($hasThread): ?>
    <a href="<?= $detailUrl ?>" class="mt-2 inline-flex items-center gap-1 text-xs font-mono text-muted">
      Show thread (<?= $threadCount ?> posts)
    </a>
    <?php endif ?>
  </div>

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
    <a href="<?= $quotedPostUrl ?>" target="_blank" rel="noopener noreferrer"
       class="col-start-2 mt-3 grid grid-cols-[1.5rem_1fr] gap-x-2 rounded-medium border border-border bg-subtle p-3 transition-colors hover:border-accent">
      <?php if (!empty($quotedPost['author_avatar'])): ?>
      <img src="<?= htmlspecialchars($quotedPost['author_avatar'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="size-6 rounded-small object-cover">
      <?php else: ?>
      <div class="size-6 rounded-small bg-border"></div>
      <?php endif ?>
      <div class="flex items-center gap-2 text-sm">
        <span class="font-medium text-primary"><?= htmlspecialchars($quotedPost['author_name'] ?: $quotedPost['author_handle'], ENT_QUOTES, 'UTF-8') ?></span>
        <span class="text-muted">@<?= htmlspecialchars($quotedPost['author_handle'], ENT_QUOTES, 'UTF-8') ?></span>
      </div>
      <div class="col-start-2 prose prose-sm prose-neutral prose-card mt-1 max-w-none text-secondary"><?= nl2br(htmlspecialchars($quotedPost['text'], ENT_QUOTES, 'UTF-8')) ?></div>
    </a>
    <?php endif ?>
  <?php endif ?>

  <?php if ($item->external_link()->isNotEmpty()): ?>
    <?php $link = json_decode($item->external_link()->value(), true); ?>
    <?php if ($link): ?>
    <a href="<?= htmlspecialchars($link['uri'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
       class="col-start-2 mt-3 block overflow-hidden rounded-medium border border-border transition-colors hover:border-accent">
      <?php if (!empty($link['thumb'])): ?>
      <img src="<?= htmlspecialchars($link['thumb'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="aspect-[2/1] w-full object-cover">
      <?php endif ?>
      <div class="p-3">
        <div class="line-clamp-1 font-medium text-primary"><?= htmlspecialchars($link['title'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php if (!empty($link['description'])): ?>
        <div class="mt-1 line-clamp-2 text-sm text-secondary"><?= htmlspecialchars($link['description'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif ?>
        <div class="mt-2 text-xs text-tertiary"><?= parse_url($link['uri'], PHP_URL_HOST) ?></div>
      </div>
    </a>
    <?php endif ?>
  <?php endif ?>

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
