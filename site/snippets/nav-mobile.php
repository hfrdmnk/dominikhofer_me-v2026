<?php
/** @var Kirby\Cms\Site $site */
$navItems = [
  ['url' => url('about'), 'label' => '/about', 'slug' => 'about'],
  ['url' => url('now'), 'label' => '/now', 'slug' => 'now'],
  ['url' => url('slash'), 'label' => '/slash', 'slug' => 'slash'],
];
?>
<nav
  id="mobile-menu"
  class="fixed inset-0 z-40 hidden flex-col bg-bg-secondary/80 p-6 backdrop-blur-lg"
  data-mobile-menu
>
  <ul class="flex flex-1 flex-col justify-center gap-6 font-mono text-base">
    <?php foreach ($navItems as $item):
      $isActive = $page->slug() === $item['slug'];
    ?>
    <li>
      <a
        href="<?= $item['url'] ?>"
        class="block <?= $isActive ? 'text-accent' : 'text-muted' ?> transition-colors hover:text-accent"
      >
        <?= $item['label'] ?>
      </a>
    </li>
    <?php endforeach ?>
  </ul>

  <div class="flex flex-col items-center gap-4 text-center">
    <?php snippet('social-icons', ['alwaysShow' => true, 'outlined' => true]) ?>

    <?php if ($site->quote_right()->isNotEmpty()): ?>
    <p class="font-mono text-sm text-accent">
      <?= $site->quote_right() ?>
    </p>
    <?php endif ?>
  </div>
</nav>
