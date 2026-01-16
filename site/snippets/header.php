<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */
$headerImage = $site->header_image()->toFile();
$profileImage = $site->author_image()->toFile();
$currentFilter = $page->isHomePage() ? 'all' : $page->slug();
?>
<header id="site-header">
  <?php if ($headerImage): ?>
  <div class="relative h-72 overflow-hidden">
    <img
      src="<?= $headerImage->resize(1200)->url() ?>"
      alt=""
      class="h-full w-full object-cover"
    >
    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
  </div>
  <?php else: ?>
  <div class="h-72 bg-gradient-to-br from-cyan-400 to-purple-500"></div>
  <?php endif ?>

  <div class="relative px-4">
    <!-- Profile section: image, name/tagline, and social icons bottom-aligned -->
    <div class="-mt-12 flex items-end justify-between gap-4">
      <div class="flex items-end gap-4">
        <?php if ($profileImage): ?>
        <img
          src="<?= $profileImage->crop(256, 256)->url() ?>"
          alt="<?= $site->author_name() ?>"
          class="h-28 w-28 shrink-0 rounded-medium border-4 border-bg-secondary bg-bg-secondary object-cover"
        >
        <?php endif ?>
        <div>
          <h1 class="text-lg font-semibold text-primary"><?= $site->author_name() ?></h1>
          <?php if ($site->author_tagline()->isNotEmpty()): ?>
          <p class="text-sm text-muted"><?= $site->author_tagline() ?></p>
          <?php endif ?>
        </div>
      </div>

      <div class="flex shrink-0 items-center gap-3">
        <?php snippet('social-icons', ['outlined' => true]) ?>
        <button
          type="button"
          class="hidden cursor-pointer items-center gap-2 rounded-small bg-accent px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-accent-hover md:flex"
          data-follow-trigger
        >
          Follow
        </button>
      </div>
    </div>

    <?php if ($site->author_bio()->isNotEmpty()): ?>
    <p class="mt-6 text-sm leading-relaxed text-secondary">
      <?= $site->author_bio()->escape() ?>
    </p>
    <?php endif ?>

    <?php snippet('tabs', ['current' => $currentFilter]) ?>
  </div>
</header>

<?php snippet('nav-mobile') ?>
