<?php
/** @var Kirby\Cms\Site $site */
$newsletterUrl = $site->newsletter_url();
$rssFeeds = $site->rss_feeds()->toStructure();
?>
<dialog
  id="follow-modal"
  class="relative m-auto w-full max-w-md rounded-big bg-bg-secondary p-0 shadow-xl backdrop:bg-black/50"
  data-follow-modal
>
  <div class="p-6">
    <div class="mb-6 flex items-center justify-between">
      <h2 class="text-lg font-medium text-primary">Follow</h2>
      <button
        type="button"
        class="text-muted transition-colors hover:text-accent"
        aria-label="Close"
        data-follow-modal-close
      >
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <?php if ($newsletterUrl->isNotEmpty()): ?>
    <div class="mb-6">
      <h3 class="mb-2 text-sm font-medium text-primary">Newsletter</h3>
      <a
        href="<?= $newsletterUrl ?>"
        target="_blank"
        rel="noopener noreferrer"
        class="flex items-center gap-3 rounded-medium border border-border p-4 transition-colors hover:border-accent hover:bg-accent-bg"
      >
        <?php snippet('icon', ['name' => 'mail', 'class' => 'h-5 w-5 shrink-0 text-accent']) ?>
        <div>
          <span class="block font-medium text-primary">Subscribe via Email</span>
          <span class="text-sm text-muted">Get updates in your inbox</span>
        </div>
      </a>
    </div>
    <?php endif ?>

    <?php if ($rssFeeds->isNotEmpty()): ?>
    <div>
      <h3 class="mb-2 text-sm font-medium text-primary">RSS Feeds</h3>
      <div class="space-y-2">
        <?php foreach ($rssFeeds as $feed): ?>
        <button
          type="button"
          data-rss-copy
          data-rss-url="<?= $feed->url() ?>"
          class="flex w-full items-center gap-3 rounded-medium border border-border p-3 text-left transition-colors hover:border-accent hover:bg-accent-bg"
        >
          <?php snippet('icon', ['name' => 'rss', 'class' => 'h-4 w-4 shrink-0 text-accent']) ?>
          <div class="min-w-0 flex-1">
            <span class="block font-medium text-primary"><?= $feed->name() ?></span>
            <?php if ($feed->description()->isNotEmpty()): ?>
            <span class="block truncate text-sm text-muted"><?= $feed->description() ?></span>
            <?php endif ?>
          </div>
        </button>
        <?php endforeach ?>
      </div>
    </div>
    <?php endif ?>
  </div>
</dialog>
