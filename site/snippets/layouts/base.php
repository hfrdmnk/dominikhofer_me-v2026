<!DOCTYPE html>
<html lang="en">
<head>
  <?php snippet('head') ?>
</head>
<body class="min-h-screen bg-(--bg) text-(--text-primary) antialiased">
  <?php snippet($header ?? 'header') ?>

  <main>
    <?= $slot ?>
  </main>

  <?php snippet('footer') ?>
  <?php snippet('follow-modal') ?>

  <script src="<?= url('assets/js/app.js') ?>" defer></script>
</body>
</html>
