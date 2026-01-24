<?php
/** @var Kirby\Cms\Site $site */
/** @var Kirby\Cms\Page $page */
$currentFilter = $page->slug();
?>
<header>
  <div class="px-4">
    <div class="flex h-14 items-center justify-between">
      <a href="<?= $site->url() ?>" class="font-medium text-primary">
        <?= $site->author_name() ?>
      </a>

      <div class="flex items-center gap-3">
        <?php snippet('social-icons', ['outlined' => true]) ?>
        <button
          type="button"
          class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-small bg-accent text-white transition-colors hover:bg-accent-hover md:hidden"
          data-follow-trigger
          aria-label="Follow"
        >
          <?php snippet('icon', ['name' => 'subscribe', 'class' => 'h-4 w-4']) ?>
        </button>
        <button
          type="button"
          class="hidden cursor-pointer items-center gap-2 rounded-small bg-accent px-5 py-2 text-sm font-medium text-white transition-colors hover:bg-accent-hover md:flex"
          data-follow-trigger
        >
          Follow
        </button>

<?php snippet('burger-button') ?>
      </div>
    </div>

    <?php snippet('tabs', ['current' => $currentFilter]) ?>
  </div>
</header>

<?php snippet('nav-mobile') ?>
