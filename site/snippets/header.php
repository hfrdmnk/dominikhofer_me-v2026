<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */
$headerImage = $site->header_image()->toFile();
$profileImage = $site->author_image()->toFile();
$currentFilter = $page->isHomePage() ? 'all' : $page->slug();
?>
<header id="site-header" class="relative" data-header="full">
  <div class="absolute inset-x-0 top-0 z-20 px-4 py-4">
    <div class="flex items-center justify-between">
      <a href="<?= $site->url() ?>" class="block" aria-label="Home">
        <?php snippet('icon', ['name' => 'logo', 'class' => 'h-8 w-8 text-(--accent)']) ?>
      </a>

      <div class="flex items-center gap-6">
        <?php snippet('nav') ?>
        <button type="button" class="md:hidden" aria-label="Open menu" data-mobile-menu-trigger>
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>
    </div>
  </div>

  <?php if ($headerImage): ?>
  <div class="relative h-48 overflow-hidden rounded-t-(--radius-big)">
    <img
      src="<?= $headerImage->resize(1200)->url() ?>"
      alt=""
      class="h-full w-full object-cover"
    >
    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
  </div>
  <?php else: ?>
  <div class="h-48 rounded-t-(--radius-big) bg-gradient-to-br from-cyan-400 to-purple-500"></div>
  <?php endif ?>

  <div class="relative px-4">
    <div class="-mt-12 flex items-end gap-4">
      <?php if ($profileImage): ?>
      <img
        src="<?= $profileImage->crop(160, 160)->url() ?>"
        alt="<?= $site->author_name() ?>"
        class="h-20 w-20 shrink-0 rounded-full border-4 border-(--bg-secondary) bg-(--bg-secondary) object-cover"
      >
      <?php endif ?>

      <div class="flex flex-1 items-center justify-between pb-2">
        <div>
          <h1 class="text-lg font-semibold text-(--text-primary)"><?= $site->author_name() ?></h1>
          <?php if ($site->author_tagline()->isNotEmpty()): ?>
          <p class="text-sm text-(--text-muted)"><?= $site->author_tagline() ?></p>
          <?php endif ?>
        </div>

        <button
          type="button"
          class="hidden shrink-0 cursor-pointer items-center gap-2 rounded-(--radius-medium) bg-(--accent) px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-(--accent-hover) md:flex"
          data-follow-trigger
        >
          <?php snippet('icon', ['name' => 'subscribe', 'class' => 'h-4 w-4']) ?>
          <span>Follow</span>
        </button>
      </div>
    </div>

    <?php if ($site->author_bio()->isNotEmpty()): ?>
    <p class="mt-4 text-sm leading-relaxed text-(--text-secondary)">
      <?= $site->author_bio()->escape() ?>
    </p>
    <?php endif ?>

    <div class="mt-4 flex items-center gap-4">
      <?php snippet('social-icons') ?>
      <button
        type="button"
        class="ml-auto cursor-pointer items-center gap-2 rounded-(--radius-medium) bg-(--accent) px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-(--accent-hover) md:hidden flex"
        data-follow-trigger
      >
        <?php snippet('icon', ['name' => 'subscribe', 'class' => 'h-4 w-4']) ?>
      </button>
    </div>

    <?php snippet('tabs', ['current' => $currentFilter]) ?>
  </div>
</header>

<header id="site-header-collapsed" class="sticky top-0 z-30 hidden border-b border-(--border) bg-(--bg-secondary)" data-header="collapsed">
  <div class="px-4">
    <div class="flex h-14 items-center justify-between">
      <a href="<?= $site->url() ?>" class="font-semibold text-(--text-primary)">
        <?= $site->author_name() ?>
      </a>

      <button
        type="button"
        class="cursor-pointer items-center gap-2 rounded-(--radius-medium) bg-(--accent) px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-(--accent-hover) hidden md:flex"
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
