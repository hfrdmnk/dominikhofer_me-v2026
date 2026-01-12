<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */
$currentFilter = $page->slug();
?>
<header class="sticky top-0 z-30 border-b border-(--border) bg-(--bg-secondary)">
  <div class="mx-auto max-w-(--container-prose) px-4">
    <div class="flex h-14 items-center justify-between">
      <a href="<?= $site->url() ?>" class="font-semibold text-(--text-primary)">
        <?= $site->author_name() ?>
      </a>

      <button
        type="button"
        class="hidden cursor-pointer items-center gap-2 rounded-(--radius-medium) bg-(--accent) px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-(--accent-hover) md:flex"
        data-follow-trigger
      >
        <?php snippet('icon', ['name' => 'subscribe', 'class' => 'h-4 w-4']) ?>
        <span>Follow</span>
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

    <?php snippet('tabs', ['current' => $currentFilter]) ?>
  </div>
</header>

<?php snippet('nav-mobile') ?>
