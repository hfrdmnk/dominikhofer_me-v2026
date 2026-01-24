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
      'pattern' => 'rss',
      'action' => function () {
        $feed = new RssFeed();
        return $feed->home();
      }
    ],
    // Posts feed
    [
      'pattern' => 'posts/rss',
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('posts');
      }
    ],
    // Notes feed
    [
      'pattern' => 'notes/rss',
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('notes');
      }
    ],
    // Photos feed
    [
      'pattern' => 'photos/rss',
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('photos');
      }
    ],
    // Races feed
    [
      'pattern' => 'races/rss',
      'action' => function () {
        $feed = new RssFeed();
        return $feed->section('races');
      }
    ]
  ]
]);
