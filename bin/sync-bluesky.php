<?php
/**
 * CLI script to sync Bluesky posts
 * Usage: php bin/sync-bluesky.php
 */

require __DIR__ . '/../kirby/bootstrap.php';

$kirby = new Kirby();
$kirby->site()->find('notes')?->getBlueskyPosts(true);

echo "Bluesky sync complete\n";
