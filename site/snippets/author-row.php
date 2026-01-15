<?php
/**
 * Author row snippet
 * @param Kirby\Cms\Page $item - The content page
 * @param bool $showReadTime - Whether to show reading time (for posts)
 */
$profileImage = $site->author_image()->toFile();
$date = $item->date()->toDate('M j, Y');
$readTime = $showReadTime ?? false;
?>
<div class="flex items-center gap-3">
  <?php if ($profileImage): ?>
  <img
    src="<?= $profileImage->crop(64, 64)->url() ?>"
    alt="<?= $site->author_name() ?>"
    class="h-8 w-8 rounded-full object-cover"
  >
  <?php endif ?>

  <div class="flex flex-wrap items-center gap-x-2 text-sm">
    <span class="font-medium text-(--text-primary)"><?= $site->author_name() ?></span>
    <span class="text-(--text-muted)">&middot;</span>
    <time datetime="<?= $item->date()->toDate('c') ?>" class="text-(--text-muted)">
      <?= $date ?>
    </time>
    <?php if ($readTime && $item->body()->isNotEmpty()): ?>
    <?php $minutes = round(str_word_count(strip_tags($item->body()->kirbytext())) / 200); ?>
    <?php if ($minutes > 0): ?>
    <span class="text-(--text-muted)">&middot;</span>
    <span class="text-(--text-muted)"><?= $minutes ?> min read</span>
    <?php endif ?>
    <?php endif ?>
  </div>
</div>
