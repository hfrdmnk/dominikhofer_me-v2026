<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= $page->isHomePage() ? $site->title() : $page->title() . ' | ' . $site->title() ?></title>

<?php if ($page->excerpt()->isNotEmpty()): ?>
<meta name="description" content="<?= $page->excerpt()->escape() ?>">
<?php elseif ($site->author_bio()->isNotEmpty()): ?>
<meta name="description" content="<?= $site->author_bio()->excerpt(160)->escape() ?>">
<?php endif ?>

<link rel="icon" href="<?= url('assets/icons/favicon.svg') ?>" type="image/svg+xml">
<link rel="canonical" href="<?= $page->url() ?>">

<meta property="og:title" content="<?= $page->title()->escape() ?>">
<meta property="og:url" content="<?= $page->url() ?>">
<meta property="og:type" content="<?= $page->isHomePage() ? 'website' : 'article' ?>">
<?php if ($site->author_name()->isNotEmpty()): ?>
<meta property="og:site_name" content="<?= $site->author_name()->escape() ?>">
<?php endif ?>
<?php if ($page->excerpt()->isNotEmpty()): ?>
<meta property="og:description" content="<?= $page->excerpt()->escape() ?>">
<?php elseif ($site->author_bio()->isNotEmpty()): ?>
<meta property="og:description" content="<?= $site->author_bio()->excerpt(160)->escape() ?>">
<?php endif ?>

<?php if ($page->intendedTemplate() == 'photo' && $page->files()->first()): ?>
<meta property="og:image" content="<?= $page->files()->first()->resize(1200)->url() ?>">
<?php elseif ($page->cover()->isNotEmpty() && $page->cover()->toFile()): ?>
<meta property="og:image" content="<?= $page->cover()->toFile()->resize(1200)->url() ?>">
<?php elseif ($site->header_image()->isNotEmpty() && $site->header_image()->toFile()): ?>
<meta property="og:image" content="<?= $site->header_image()->toFile()->resize(1200)->url() ?>">
<?php endif ?>

<link rel="alternate" type="application/rss+xml" title="<?= $site->author_name() ?>" href="<?= url('rss') ?>">
<link rel="sitemap" href="<?= url('sitemap.xml') ?>">

<?php
$fediverse = $site->social_links()->toStructure()->findBy('icon', 'fediverse');
if ($fediverse):
  $url = parse_url($fediverse->url());
  $handle = ltrim($url['path'] ?? '', '/@');
  $instance = $url['host'] ?? '';
  if ($handle && $instance):
?>
<meta name="fediverse:creator" content="@<?= $handle ?>@<?= $instance ?>">
<?php endif; endif; ?>

<?php if (option('debug') === false && $site->plausible_domain()->isNotEmpty() && $site->plausible_script()->isNotEmpty()): ?>
<script defer data-domain="<?= $site->plausible_domain() ?>" src="<?= $site->plausible_script() ?>"></script>
<?php endif ?>

<?= css('assets/css/styles.css') ?>
