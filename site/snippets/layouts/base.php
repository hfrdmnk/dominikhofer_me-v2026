<!DOCTYPE html>
<html lang="en">
<head>
  <?php snippet('head') ?>
</head>
<body class="min-h-screen bg-bg text-primary antialiased">
  <div class="mx-auto max-w-3xl px-4 py-6 md:py-10">
    <!-- Top bar: Logo + Navigation (outside white card) -->
    <div class="mb-4 flex items-baseline justify-between">
      <a href="<?= $site->url() ?>" class="block" aria-label="Home">
        <?php snippet('icon', ['name' => 'logo', 'class' => 'h-10 w-10 text-accent']) ?>
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

    <!-- White card container -->
    <div class="overflow-hidden rounded-big bg-bg-secondary">
      <?php snippet($header ?? 'header') ?>
      <main>
        <?= $slot ?>
      </main>
    </div>
  </div>

  <?php snippet('footer') ?>
  <?php snippet('follow-modal') ?>

  <script src="<?= url('assets/js/app.js') ?>" defer></script>
</body>
</html>
