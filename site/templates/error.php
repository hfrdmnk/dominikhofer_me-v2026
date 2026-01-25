<?php
/**
 * 404 Error page template
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site
 */

// Latest content from each section
$latestPost = page('posts')?->children()->listed()->sortBy('date', 'desc')->first();
$latestNote = page('notes')?->children()->listed()->sortBy('date', 'desc')->first();
$latestPhoto = page('photos')?->children()->listed()->sortBy('date', 'desc')->first();
$latestRace = page('races')?->children()->listed()->sortBy('date', 'desc')->first();

// Get email from site social links
$emailLink = $site->social_links()->toStructure()->filter(
  fn($link) => Str::startsWith($link->url(), 'mailto:')
)->first();
?>
<?php snippet('layouts/base', ['header' => 'header-single'], slots: true) ?>
  <article class="px-4 py-8">
    <div class="prose prose-neutral max-w-none">
    <h2>Oops! Page not found.</h2>
      <p>
        It looks like either you or I made an error (probably me).
        Wanna go back to the <a href="<?= $site->url() ?>">home page</a>?
      </p>

      <?php if ($latestPost || $latestNote || $latestPhoto || $latestRace): ?>
      <p>Or check out some of my latest content:</p>
      <ul>
        <?php if ($latestPost): ?>
        <li><a href="<?= $latestPost->url() ?>">Read my latest post</a></li>
        <?php endif ?>
        <?php if ($latestNote): ?>
        <li><a href="<?= $latestNote->url() ?>">Read my latest note</a></li>
        <?php endif ?>
        <?php if ($latestPhoto): ?>
        <li><a href="<?= $latestPhoto->url() ?>">View my latest photo</a></li>
        <?php endif ?>
        <?php if ($latestRace): ?>
        <li><a href="<?= $latestRace->url() ?>">See my latest race result</a></li>
        <?php endif ?>
      </ul>
      <?php endif ?>

      <?php if ($emailLink): ?>
      <p>
        Or <a href="<?= $emailLink->url() ?>">write me an email</a> and say hi :)
      </p>
      <?php endif ?>
    </div>
  </article>
<?php endsnippet() ?>
