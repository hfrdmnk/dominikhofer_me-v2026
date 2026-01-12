<?php
/**
 * Feed item wrapper - dispatches to correct card based on template
 * @param Kirby\Cms\Page $item - The content page
 */
$template = $item->intendedTemplate()->name();
$cardSnippet = match($template) {
  'post' => 'cards/post',
  'note' => 'cards/note',
  'photo' => 'cards/photo',
  'race' => 'cards/race',
  default => null,
};
?>
<?php if ($cardSnippet): ?>
<div class="border-b border-(--border) py-6 first:pt-0 last:border-b-0">
  <?php snippet($cardSnippet, ['item' => $item]) ?>
</div>
<?php endif ?>
