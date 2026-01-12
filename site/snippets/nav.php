<?php
$navItems = [
  ['url' => url('about'), 'label' => 'About'],
  ['url' => url('now'), 'label' => 'Now'],
  ['url' => url('slash'), 'label' => 'Slash'],
];
?>
<nav class="hidden items-center gap-6 md:flex">
  <?php foreach ($navItems as $item): ?>
  <a
    href="<?= $item['url'] ?>"
    class="text-sm text-(--text-secondary) transition-colors hover:text-(--text-primary)"
  >
    <?= $item['label'] ?>
  </a>
  <?php endforeach ?>
</nav>
