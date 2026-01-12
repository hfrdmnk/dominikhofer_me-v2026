<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */

$template = $page->intendedTemplate()->name();
$parentLabels = [
  'post' => 'view all posts',
  'note' => 'view all notes',
  'photo' => 'view all photos',
  'race' => 'view all races',
];
$parentLabel = $parentLabels[$template] ?? null;
$parentUrl = match($template) {
  'post' => url('posts'),
  'note' => url('notes'),
  'photo' => url('photos'),
  'race' => url('races'),
  default => $site->url(),
};
$backUrl = $page->parent()?->url() ?? $site->url();
?>
<header class="sticky top-0 z-30 border-b border-(--border) bg-(--bg-secondary)">
  <div class="mx-auto max-w-(--container-prose) px-4">
    <div class="flex min-h-14 items-center gap-4 py-3">
      <a
        href="<?= $backUrl ?>"
        class="shrink-0 text-(--text-muted) transition-colors hover:text-(--text-primary)"
        aria-label="Go back"
      >
        <?php snippet('icon', ['name' => 'back', 'class' => 'h-5 w-5']) ?>
      </a>

      <div class="min-w-0 flex-1">
        <?php if ($parentLabel): ?>
        <a href="<?= $parentUrl ?>" class="block font-mono text-xs uppercase tracking-wide text-(--text-muted) transition-colors hover:text-(--accent)">
          <?= $parentLabel ?>
        </a>
        <?php endif ?>
        <h1 class="truncate text-base font-semibold text-(--text-primary)">
          <?= $page->title() ?>
        </h1>
      </div>

      <?php snippet('share-button') ?>
    </div>
  </div>
</header>

<?php snippet('nav-mobile') ?>
