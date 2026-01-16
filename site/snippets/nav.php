<?php
$navItems = [
  ['url' => url('about'), 'label' => '/about'],
  ['url' => url('now'), 'label' => '/now'],
  ['url' => url('slash'), 'label' => '/slash'],
];
?>
<nav class="hidden items-bottom gap-6 font-mono text-xs md:flex">
  <?php foreach ($navItems as $item): ?>
  <a
    href="<?= $item['url'] ?>"
    class="text-muted transition-colors hover:text-accent"
  >
    <?= $item['label'] ?>
  </a>
  <?php endforeach ?>
</nav>
