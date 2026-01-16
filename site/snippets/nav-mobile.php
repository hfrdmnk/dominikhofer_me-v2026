<?php
/** @var Kirby\Cms\Site $site */
$navItems = [
  ['url' => url('about'), 'label' => 'About'],
  ['url' => url('now'), 'label' => 'Now'],
  ['url' => url('slash'), 'label' => 'Slash'],
];
?>
<div
  id="mobile-menu"
  class="fixed inset-0 z-40 hidden"
  data-mobile-menu
>
  <div class="absolute inset-0 bg-black/50" data-mobile-menu-backdrop></div>

  <nav class="absolute bottom-0 right-0 top-0 w-72 max-w-[80vw] bg-bg-secondary p-6 shadow-xl">
    <div class="mb-8 flex justify-end">
      <button
        type="button"
        class="text-muted transition-colors hover:text-primary"
        aria-label="Close menu"
        data-mobile-menu-close
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <ul class="space-y-4">
      <?php foreach ($navItems as $item): ?>
      <li>
        <a
          href="<?= $item['url'] ?>"
          class="block text-lg text-primary transition-colors hover:text-accent"
        >
          <?= $item['label'] ?>
        </a>
      </li>
      <?php endforeach ?>
    </ul>

    <hr class="my-6 border-border">

    <div class="flex items-center gap-3">
      <?php snippet('social-icons') ?>
    </div>

    <button
      type="button"
      class="mt-6 flex w-full cursor-pointer items-center justify-center gap-2 rounded-medium bg-accent px-4 py-3 text-sm font-medium text-white transition-colors hover:bg-accent-hover"
      data-follow-trigger
    >
      <?php snippet('icon', ['name' => 'subscribe', 'class' => 'h-4 w-4']) ?>
      <span>Follow</span>
    </button>

    <?php if ($site->quote_right()->isNotEmpty()): ?>
    <p class="mt-8 font-mono text-sm text-accent">
      <?= $site->quote_right() ?>
    </p>
    <?php endif ?>
  </nav>
</div>
