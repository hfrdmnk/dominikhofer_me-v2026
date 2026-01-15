<?php
$navItems = [
  ['url' => url('about'), 'label' => '/about'],
  ['url' => url('now'), 'label' => '/now'],
  ['url' => url('slash'), 'label' => '/slash'],
];
?>
<nav class="hidden items-center gap-6 font-mono text-sm md:flex">
  <?php foreach ($navItems as $item): ?>
  <a
    href="<?= $item['url'] ?>"
    class="text-(--text-muted) transition-colors hover:text-(--text-primary)"
  >
    <?= $item['label'] ?>
  </a>
  <?php endforeach ?>
</nav>
