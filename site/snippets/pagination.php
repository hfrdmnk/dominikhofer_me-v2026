<?php
/** @var Kirby\Toolkit\Pagination $pagination */
if ($pagination->hasPages()): ?>
<nav class="mt-6 flex justify-center gap-4">
  <?php if ($pagination->hasPrevPage()): ?>
  <a href="<?= $pagination->prevPageUrl() ?>" class="rounded-medium border border-border px-4 py-2 text-sm text-muted transition-colors hover:border-accent hover:text-accent">
    Newer
  </a>
  <?php endif ?>
  <?php if ($pagination->hasNextPage()): ?>
  <a href="<?= $pagination->nextPageUrl() ?>" class="rounded-medium border border-border px-4 py-2 text-sm text-muted transition-colors hover:border-accent hover:text-accent">
    Older
  </a>
  <?php endif ?>
</nav>
<?php endif ?>
