<?php
/**
 * Author row snippet (outputs two sibling elements for grid layout)
 * @param Kirby\Cms\Page $item - The content page
 * @param string|null $linkUrl - Optional URL to link the timestamp
 * @param bool $relativeDate - Use relative date (true) or formatted date (false)
 * @param string|null $metadata - Optional metadata text (e.g., reading time, location)
 */
$profileImage = $site->author_image()->toFile();
$linkUrl = $linkUrl ?? null;
$relativeDate = $relativeDate ?? true;
$metadata = $metadata ?? null;
$displayDate = $relativeDate ? $item->timeAgo() : $item->formattedDate();
?>
<?php if ($profileImage): ?>
<img
  src="<?= $profileImage->crop(64, 64)->url() ?>"
  alt="<?= $site->author_name() ?>"
  class="h-8 w-8 rounded-small object-cover"
>
<?php endif ?>

<div class="flex items-center justify-between">
  <div class="flex items-center gap-2">
    <span class="font-medium text-primary text-sm"><?= $site->author_name() ?></span>
    <?php if ($linkUrl): ?>
    <a href="<?= $linkUrl ?>" class="font-mono text-muted hover:text-accent transition-colors text-xs">
      <time datetime="<?= $item->date()->toDate('c') ?>"><?= $displayDate ?></time>
    </a>
    <?php else: ?>
    <time datetime="<?= $item->date()->toDate('c') ?>" class="font-mono text-muted text-xs"><?= $displayDate ?></time>
    <?php endif ?>
  </div>
  <?php if ($metadata): ?>
  <span class="font-mono text-muted text-xs"><?= $metadata ?></span>
  <?php endif ?>
</div>
