<?php

/**
 * RSS Feed Plugin
 *
 * Provides RSS feeds for all content types.
 * Appending /rss to any section URL returns an RSS feed for that section.
 */

require_once __DIR__ . '/RssFeed.php';

Kirby::plugin('dominik/rss', [
  'routes' => [
    // Home feed - all content types
    [
      'pattern' => ['rss', 'rss.xml'],
      'action' => function () {
        $feed = new RssFeed();
        return $feed->home();
      }
    ],
    // Posts feed
    [
      'pattern' => ['posts/rss', 'posts/rss.xml'],
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('posts');
      }
    ],
    // Notes feed
    [
      'pattern' => ['notes/rss', 'notes/rss.xml'],
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('notes');
      }
    ],
    // Photos feed
    [
      'pattern' => ['photos/rss', 'photos/rss.xml'],
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('photos');
      }
    ],
    // Races feed
    [
      'pattern' => ['races/rss', 'races/rss.xml'],
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('races');
      }
    ]
  ]
]);
