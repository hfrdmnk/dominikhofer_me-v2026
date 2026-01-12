<!DOCTYPE html>
<html lang="en">
<head>
  <?php snippet('head') ?>
</head>
<body class="min-h-screen bg-(--bg) text-(--text-primary) antialiased">
  <div class="mx-auto max-w-3xl px-4 py-6 md:py-10">
    <div class="overflow-hidden rounded-(--radius-big) bg-(--bg-secondary)">
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
