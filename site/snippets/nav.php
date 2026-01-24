<?php
$navItems = [
  ['url' => url('about'), 'label' => '/about', 'slug' => 'about'],
  ['url' => url('now'), 'label' => '/now', 'slug' => 'now'],
  ['url' => url('slash'), 'label' => '/slash', 'slug' => 'slash'],
];
?>
<nav class="hidden items-bottom gap-6 font-mono text-xs md:flex">
  <?php foreach ($navItems as $item):
    $isActive = $page->slug() === $item['slug'];
  ?>
  <a
    href="<?= $item['url'] ?>"
    class="<?= $isActive ? 'text-accent' : 'text-muted' ?> transition-colors hover:text-accent"
  >
    <?= $item['label'] ?>
  </a>
  <?php endforeach ?>
</nav>
