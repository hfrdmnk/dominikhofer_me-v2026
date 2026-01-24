<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */

$template = $page->intendedTemplate()->name();

// Custom titles for certain templates
$displayTitle = match($template) {
  'note' => 'Note',
  'photo' => 'Photo',
  default => $page->title(),
};

// Show slug for all pages
$slug = '/' . $page->slug();
?>

<header class="border-b border-border">
  <div class="px-4">
    <div class="flex min-h-14 items-center justify-between gap-4 py-3">
      <div class="flex min-w-0 items-center gap-3">
        <!-- Back button - shown inline on smaller screens -->
        <button
          type="button"
          class="flex h-9 w-9 shrink-0 cursor-pointer items-center justify-center rounded-small border border-border text-muted transition-colors hover:border-accent hover:text-accent lg:hidden"
          aria-label="Go back"
          data-back-button
          data-fallback-url="<?= $site->url() ?>"
        >
          <?php snippet('icon', ['name' => 'back', 'class' => 'h-4 w-4']) ?>
        </button>

        <!-- Title area with slug above -->
        <div class="flex min-w-0 flex-col">
          <span class="truncate font-mono text-xs text-accent">
            <?= $slug ?>
          </span>
          <h1 class="truncate text-base font-medium text-primary">
            <?= $displayTitle ?>
          </h1>
        </div>
      </div>

      <div class="flex shrink-0 items-center gap-2">
        <?php snippet('social-icons', ['outlined' => true]) ?>

        <!-- Follow icon button (accent filled) -->
        <button
          type="button"
          class="hidden h-9 w-9 cursor-pointer items-center justify-center rounded-small bg-accent text-white transition-colors hover:bg-accent-hover md:flex"
          data-follow-trigger
          aria-label="Follow"
        >
          <?php snippet('icon', ['name' => 'subscribe', 'class' => 'h-4 w-4']) ?>
        </button>

        <button
          type="button"
          class="md:hidden"
          aria-label="Open menu"
          data-mobile-menu-trigger
        >
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</header>

<?php snippet('nav-mobile') ?>
