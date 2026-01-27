<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php
$titleSuffix = $site->title_suffix()->or($site->author_name())->value();
$displayTitle = match($page->intendedTemplate()->name()) {
  'note' => 'Note',
  'photo' => 'Photo',
  default => $page->title()->value(),
};
?>
<title><?= $page->isHomePage() ? $titleSuffix : $displayTitle . ' | ' . $titleSuffix ?></title>

<?php if ($page->excerpt()->isNotEmpty()): ?>
<meta name="description" content="<?= $page->excerpt() ?>">
<?php elseif ($site->author_bio()->isNotEmpty()): ?>
<meta name="description" content="<?= $site->author_bio()->excerpt(160) ?>">
<?php endif ?>

<link rel="icon" href="<?= url('assets/icons/favicon.svg') ?>" type="image/svg+xml">
<link rel="canonical" href="<?= $page->url() ?>">

<meta property="og:title" content="<?= $page->isHomePage() ? $titleSuffix : $displayTitle . ' | ' . $titleSuffix ?>">
<meta property="og:url" content="<?= $page->url() ?>">
<meta property="og:type" content="<?= $page->isHomePage() ? 'website' : 'article' ?>">
<?php if ($site->author_name()->isNotEmpty()): ?>
<meta property="og:site_name" content="<?= $site->author_name()->escape() ?>">
<?php endif ?>
<?php if ($page->excerpt()->isNotEmpty()): ?>
<meta property="og:description" content="<?= $page->excerpt() ?>">
<?php elseif ($site->author_bio()->isNotEmpty()): ?>
<meta property="og:description" content="<?= $site->author_bio()->excerpt(160) ?>">
<?php endif ?>

<?php if ($page->intendedTemplate() == 'photo' && $image = $page->files()->first()): ?>
<meta property="og:image" content="<?= $image->resize(1200)->url() ?>">
<meta property="og:image:width" content="<?= $image->resize(1200)->width() ?>">
<meta property="og:image:height" content="<?= $image->resize(1200)->height() ?>">
<?php elseif ($page->cover()->isNotEmpty() && $image = $page->cover()->toFile()): ?>
<meta property="og:image" content="<?= $image->resize(1200)->url() ?>">
<meta property="og:image:width" content="<?= $image->resize(1200)->width() ?>">
<meta property="og:image:height" content="<?= $image->resize(1200)->height() ?>">
<?php elseif ($page->intendedTemplate() == 'note'):
  // Check media_1 through media_4 for first image
  $ogImage = null;
  foreach (['media_1', 'media_2', 'media_3', 'media_4'] as $field) {
    if ($page->$field()->isNotEmpty()) {
      $file = $page->$field()->toFile();
      if ($file && $file->type() === 'image') {
        $ogImage = $file;
        break;
      }
    }
  }
  if ($ogImage):
?>
<meta property="og:image" content="<?= $ogImage->resize(1200)->url() ?>">
<meta property="og:image:width" content="<?= $ogImage->resize(1200)->width() ?>">
<meta property="og:image:height" content="<?= $ogImage->resize(1200)->height() ?>">
<?php else: ?>
<meta property="og:image" content="<?= $page->url() . '.png' ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<?php endif; ?>
<?php else: ?>
<meta property="og:image" content="<?= $page->isHomePage() ? url('home.png') : $page->url() . '.png' ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<?php endif ?>

<link rel="alternate" type="application/rss+xml" title="<?= $site->author_name() ?>" href="<?= url('rss') ?>">
<link rel="sitemap" href="<?= url('sitemap.xml') ?>">

<?php
$mastodon = $site->social_links()->toStructure()->findBy('icon', 'mastodon');
$fediverse = $mastodon ?? $site->social_links()->toStructure()->findBy('icon', 'fediverse');
if ($fediverse):
  $url = parse_url($fediverse->url());
  $handle = ltrim($url['path'] ?? '', '/@');
  $instance = $url['host'] ?? '';
  if ($handle && $instance):
?>
<meta name="fediverse:creator" content="@<?= $handle ?>@<?= $instance ?>">
<?php endif; endif; ?>

<?php
// rel="me" links for Mastodon/Fediverse profile verification
foreach ($site->social_links()->toStructure() as $link):
  if (in_array($link->icon()->value(), ['mastodon', 'fediverse'])):
?>
<link rel="me" href="<?= $link->url() ?>">
<?php endif; endforeach; ?>

<?php if (option('debug') === false && $site->plausible_domain()->isNotEmpty() && $site->plausible_script()->isNotEmpty()): ?>
<script defer data-domain="<?= $site->plausible_domain() ?>" src="<?= $site->plausible_script() ?>"></script>
<?php endif ?>

<?= vite()->css('src/main.css') ?>
