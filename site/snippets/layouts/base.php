<?php
$template = $page->intendedTemplate()->name();
$listingPages = ['home', 'posts', 'notes', 'photos', 'races'];
$isDetailPage = !in_array($template, $listingPages);
$logoClass = $isDetailPage
  ? 'h-10 w-10 text-muted transition-colors group-hover:text-accent'
  : 'h-10 w-10 text-accent';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php snippet('head') ?>
</head>
<body class="min-h-screen bg-bg text-primary antialiased">
  <div class="mx-auto max-w-3xl px-4 py-6 md:py-10">
    <!-- Top bar: Logo + Navigation -->
    <div class="mb-4 flex items-baseline justify-between">
      <a href="<?= $site->url() ?>" class="group block" aria-label="Home">
        <?php snippet('icon', ['name' => 'logo', 'class' => $logoClass]) ?>
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

    <!-- White card container with relative wrapper for external back button -->
    <div class="relative">
      <?php if ($isDetailPage): ?>
      <!-- Back button - positioned outside card on lg screens -->
      <button
        type="button"
        class="fixed left-[calc(50vw-26.5rem)] top-30 hidden h-9 w-9 cursor-pointer items-center justify-center rounded-small border border-border text-muted transition-colors hover:border-accent hover:text-accent lg:flex"
        aria-label="Go back"
        data-back-button
        data-fallback-url="<?= $site->url() ?>"
      >
        <?php snippet('icon', ['name' => 'back', 'class' => 'h-4 w-4']) ?>
      </button>
      <?php endif; ?>

      <div class="overflow-hidden rounded-big bg-bg-secondary">
        <?php snippet($header ?? 'header') ?>
        <main>
          <?= $slot ?>
        </main>
      </div>
    </div>
  </div>

  <?php snippet('footer') ?>
  <?php snippet('follow-modal') ?>

  <script src="<?= url('assets/js/app.js') ?>" defer></script>
</body>
</html>
