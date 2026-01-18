<?php
/**
 * Card footer with metadata and share popover
 * @param Kirby\Cms\Page $item - The content page
 * @param string|null $leftContent - Optional text for left side
 * @param string $leftClass - Additional classes for left content
 */
$leftContent = $leftContent ?? null;
$leftClass = $leftClass ?? '';
$popoverId = 'share-' . $item->id();
$anchorName = '--share-' . str_replace('/', '-', $item->id());
$shareUrl = $item->url();
$shareTitle = $item->title()->value() ?: 'Check this out';
$blueskyUrl = 'https://bsky.app/intent/compose?text=' . urlencode($shareTitle . ' ' . $shareUrl);
?>
<footer class="mt-4 flex items-center justify-between text-xs">
  <div class="<?= $leftClass ?>">
    <?php if ($leftContent): ?>
    <?= $leftContent ?>
    <?php endif ?>
  </div>

  <div class="relative">
    <button
      popovertarget="<?= $popoverId ?>"
      class="font-mono text-muted hover:text-accent transition-colors cursor-pointer"
      style="anchor-name: <?= $anchorName ?>"
    >
      share
    </button>

    <div
      popover
      id="<?= $popoverId ?>"
      class="m-0 min-w-40 rounded-small border border-border p-2 shadow-md"
      style="position-anchor: <?= $anchorName ?>; inset: unset; top: anchor(bottom); right: anchor(right); margin-top: 0.5rem"
    >
      <a
        href="<?= $blueskyUrl ?>"
        target="_blank"
        rel="noopener noreferrer"
        class="block rounded-small px-3 py-2 text-sm text-secondary hover:text-accent transition-colors"
      >
        Share on Bluesky
      </a>
      <button
        data-copy-link="<?= $shareUrl ?>"
        class="block w-full rounded-small px-3 py-2 text-left text-sm text-secondary hover:text-accent transition-colors"
      >
        Copy link
      </button>
    </div>
  </div>
</footer>
