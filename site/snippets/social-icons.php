<?php
/** @var Kirby\Cms\Site $site */
$socialLinks = $site->social_links()->toStructure();
?>
<?php if ($socialLinks->isNotEmpty()): ?>
<div class="flex items-center gap-3">
  <?php foreach ($socialLinks as $link): ?>
  <a
    href="<?= $link->url() ?>"
    class="text-(--text-muted) transition-colors hover:text-(--accent)"
    <?= Str::startsWith($link->url(), 'mailto:') ? '' : 'target="_blank" rel="noopener noreferrer"' ?>
    aria-label="<?= $link->platform() ?>"
  >
    <?php snippet('icon', ['name' => $link->icon()->or('rss'), 'class' => 'h-5 w-5']) ?>
  </a>
  <?php endforeach ?>
</div>
<?php endif ?>
