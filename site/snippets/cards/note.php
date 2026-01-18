<?php
/**
 * Note card for feed
 * @param Kirby\Cms\Page $item - The note page
 */

// Collect media files
$mediaFiles = [];
for ($i = 1; $i <= 4; $i++) {
  $file = $item->{'media_' . $i}()->toFile();
  if ($file) {
    $mediaFiles[] = $file;
  }
}
$mediaCount = count($mediaFiles);
$detailUrl = $item->url();
?>
<article class="group">
  <?php snippet('author-row', ['item' => $item, 'linkUrl' => $detailUrl]) ?>

  <div class="prose prose-neutral prose-card mt-3 max-w-none leading-relaxed text-secondary">
    <?= $item->body()->kt() ?>
  </div>

  <?php if ($mediaCount > 0): ?>
  <a href="<?= $detailUrl ?>" class="mt-4 block overflow-hidden rounded-medium">
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
  <?php endif ?>

  <?php snippet('card-footer', ['item' => $item]) ?>
</article>
