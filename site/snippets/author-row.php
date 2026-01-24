<?php
/**
 * Author row snippet (outputs two sibling elements for grid layout)
 * @param Kirby\Cms\Page $item - The content page
 * @param string|null $linkUrl - Optional URL to link the timestamp
 * @param bool $relativeDate - Use relative date (true) or formatted date (false)
 */
$profileImage = $site->author_image()->toFile();
$linkUrl = $linkUrl ?? null;
$relativeDate = $relativeDate ?? true;
$showTags = $showTags ?? true;
$tags = $item->tags()->isNotEmpty() ? $item->tags()->split() : [];
$displayDate = $relativeDate ? $item->timeAgo() : $item->formattedDate();
?>
<?php if ($profileImage): ?>
<img
  src="<?= $profileImage->crop(64, 64)->url() ?>"
  alt="<?= $site->author_name() ?>"
  class="h-8 w-8 rounded-small object-cover"
>
<?php endif ?>

<div class="flex items-center justify-between gap-3">
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

  <?php if ($showTags && count($tags) > 0): ?>
  <div class="flex flex-wrap items-center gap-2 font-mono text-xs text-muted">
    <?php foreach ($tags as $tag): ?>
    <a href="<?= url('tag/' . urlencode($tag)) ?>" class="hover:text-accent transition-colors">#<?= htmlspecialchars($tag) ?></a>
    <?php endforeach ?>
  </div>
  <?php endif ?>
</div>
