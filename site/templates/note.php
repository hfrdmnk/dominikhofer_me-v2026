<?php
/**
 * Single note template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

// Check if this is a thread (body contains separator)
$bodyContent = $page->body()->value();
$bodyParts = explode("\n\n---\n\n", $bodyContent);
$isThread = count($bodyParts) > 1;

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
    <div class="grid grid-cols-[2rem_1fr] gap-x-3">
      <?php snippet('author-row', ['item' => $page, 'relativeDate' => false]) ?>
    </div>

    <div class="prose prose-neutral prose-headings:font-medium prose-strong:font-medium prose-img:rounded-small mt-6 max-w-none">
      <?php if ($isThread): ?>
        <?php foreach ($bodyParts as $index => $part): ?>
          <?php if ($index > 0): ?>
          <hr class="my-6 border-tertiary">
          <?php endif ?>
          <?= kirbytext($part) ?>
        <?php endforeach ?>
      <?php else: ?>
        <?= $page->body()->kt() ?>
      <?php endif ?>
    </div>

    <?php if ($page->quoted_post()->isNotEmpty()): ?>
      <?php $quotedPost = json_decode($page->quoted_post()->value(), true); ?>
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
         class="mt-6 grid grid-cols-[1.5rem_1fr] gap-x-2 rounded-medium border border-border bg-subtle p-3 transition-colors hover:border-accent">
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

    <?php if ($page->external_link()->isNotEmpty()): ?>
      <?php $link = json_decode($page->external_link()->value(), true); ?>
      <?php if ($link): ?>
      <a href="<?= htmlspecialchars($link['uri'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer"
         class="mt-6 block overflow-hidden rounded-medium border border-border transition-colors hover:border-accent">
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

    <?php snippet('card-footer', ['item' => $page]) ?>

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
