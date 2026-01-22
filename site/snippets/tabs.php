<?php
/** @var string $current - Current active tab (all, posts, notes, photos, races) */
$tabs = [
  ['slug' => 'all', 'url' => url('/'), 'label' => 'All'],
  ['slug' => 'posts', 'url' => url('posts'), 'label' => 'Posts'],
  ['slug' => 'notes', 'url' => url('notes'), 'label' => 'Notes'],
  ['slug' => 'photos', 'url' => url('photos'), 'label' => 'Photos'],
  ['slug' => 'races', 'url' => url('races'), 'label' => 'Races'],
];
?>
<nav class="isolate relative mt-6 -mx-4 overflow-x-auto overflow-y-hidden after:absolute after:inset-x-0 after:bottom-0 after:-z-10 after:h-px after:bg-border">
  <ul class="flex gap-6 px-4">
    <?php foreach ($tabs as $tab): ?>
    <?php $isActive = ($current ?? 'all') === $tab['slug']; ?>
    <li>
      <a
        href="<?= $tab['url'] ?>"
        class="relative block py-3 text-sm transition-colors <?= $isActive ? 'font-medium text-accent' : 'text-muted hover:text-accent' ?>"
      >
        <?= $tab['label'] ?>
        <?php if ($isActive): ?>
        <span class="absolute inset-x-0 bottom-0 h-px bg-accent"></span>
        <?php endif ?>
      </a>
    </li>
    <?php endforeach ?>
  </ul>
</nav>
