<?php
/**
 * Author row snippet
 * @param Kirby\Cms\Page $item - The content page
 * @param string|null $linkUrl - Optional URL to link the timestamp
 */
$profileImage = $site->author_image()->toFile();
$linkUrl = $linkUrl ?? null;
$tags = $item->tags()->isNotEmpty() ? $item->tags()->split() : [];
?>
<div class="flex items-center justify-between gap-3">
  <div class="flex items-center gap-3">
    <?php if ($profileImage): ?>
    <img
      src="<?= $profileImage->crop(64, 64)->url() ?>"
      alt="<?= $site->author_name() ?>"
      class="h-8 w-8 rounded-medium object-cover"
    >
    <?php endif ?>

    <div class="flex items-center gap-2 ">
      <span class="font-medium text-primary text-sm"><?= $site->author_name() ?></span>
      <?php if ($linkUrl): ?>
      <a href="<?= $linkUrl ?>" class="font-mono text-muted hover:text-primary text-xs">
        <time datetime="<?= $item->date()->toDate('c') ?>"><?= $item->timeAgo() ?></time>
      </a>
      <?php else: ?>
      <time datetime="<?= $item->date()->toDate('c') ?>" class="font-mono text-muted text-xs"><?= $item->timeAgo() ?></time>
      <?php endif ?>
    </div>
  </div>

  <?php if (count($tags) > 0): ?>
  <div class="flex flex-wrap items-center gap-2 font-mono text-xs text-muted">
    <?php foreach ($tags as $tag): ?>
    <span>#<?= $tag ?></span>
    <?php endforeach ?>
  </div>
  <?php endif ?>
</div>
