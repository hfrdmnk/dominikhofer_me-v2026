<?php
/** @var Kirby\Cms\Site $site */
/** @var bool $outlined - Whether to use outlined button style */
$socialLinks = $site->social_links()->toStructure();
$outlined = $outlined ?? false;
?>
<?php if ($socialLinks->isNotEmpty()): ?>
<div class="hidden items-center gap-2 md:flex">
  <?php foreach ($socialLinks as $link): ?>
  <?php if ($outlined): ?>
  <a
    href="<?= $link->url() ?>"
    class="flex h-9 w-9 items-center justify-center rounded-small border border-border text-muted transition-colors hover:border-accent hover:text-accent"
    <?= Str::startsWith($link->url(), 'mailto:') ? '' : 'target="_blank" rel="noopener noreferrer"' ?>
    aria-label="<?= $link->platform() ?>"
  >
    <?php snippet('icon', ['name' => $link->icon()->or('rss'), 'class' => 'h-4 w-4']) ?>
  </a>
  <?php else: ?>
  <a
    href="<?= $link->url() ?>"
    class="text-muted transition-colors hover:text-accent"
    <?= Str::startsWith($link->url(), 'mailto:') ? '' : 'target="_blank" rel="noopener noreferrer"' ?>
    aria-label="<?= $link->platform() ?>"
  >
    <?php snippet('icon', ['name' => $link->icon()->or('rss'), 'class' => 'h-5 w-5']) ?>
  </a>
  <?php endif ?>
  <?php endforeach ?>
</div>
<?php endif ?>
