<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */

$backUrl = $page->parent()?->url() ?? $site->url();
?>
<header class="border-b border-border">
  <div class="px-4">
    <div class="flex min-h-14 items-center justify-between gap-4 py-3">
      <div class="flex min-w-0 items-center gap-3">
        <a
          href="<?= $backUrl ?>"
          class="shrink-0 text-muted transition-colors hover:text-primary"
          aria-label="Go back"
        >
          <?php snippet('icon', ['name' => 'back', 'class' => 'h-5 w-5']) ?>
        </a>

        <h1 class="truncate text-base font-semibold text-primary">
          <?= $page->title() ?>
        </h1>
      </div>

      <div class="flex shrink-0 items-center gap-3">
        <?php snippet('social-icons', ['outlined' => true]) ?>
        <button
          type="button"
          class="hidden cursor-pointer items-center gap-2 rounded-medium bg-accent px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-accent-hover md:flex"
          data-follow-trigger
        >
          Follow
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
