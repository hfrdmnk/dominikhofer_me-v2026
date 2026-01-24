<?php
/** @var Kirby\Cms\Site $site */
$quoteLeft = $site->quote_left()->or('Less, but better.');
$quoteRight = $site->quote_right()->or('Trust the process.');
?>
<footer class="mt-auto py-8">
  <div class="mx-auto flex max-w-3xl flex-col items-center justify-between gap-4 px-4 md:flex-row">
    <p class="font-mono text-xs text-accent">
      <?= $quoteLeft ?>
    </p>
    <p class="hidden font-mono text-xs text-accent md:block">
      <?= $quoteRight ?>
    </p>
  </div>
</footer>
