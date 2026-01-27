<?php

/**
 * Bluesky Integration Plugin
 *
 * Imports Bluesky posts as virtual pages in the notes collection.
 * Posts are fetched from the API, cached, and appear alongside existing notes.
 */

require_once __DIR__ . '/lib/BlueskyApi.php';
require_once __DIR__ . '/lib/BlueskyParser.php';

Kirby::plugin('dominik/bluesky', [
  'options' => [
    'did' => 'did:plc:fthx2gjakdj4ynxxu5vysjty',
    'cacheTtl' => 60 * 60, // 1 hour
    'excludeDomains' => ['bsky.app', 'dominikhofer.me'],
  ],

  'sections' => [
    'bluesky' => [
      'props' => [
        'label' => function ($label = 'Bluesky Import') {
          return $label;
        }
      ],
      'computed' => [
        'lastSync' => function () {
          $cache = kirby()->cache('bluesky');
          return $cache->get('lastSync');
        },
        'postCount' => function () {
          $cache = kirby()->cache('bluesky');
          $data = $cache->get('posts');
          return $data !== null ? count($data) : 0;
        }
      ]
    ]
  ],

  'api' => [
    'routes' => [
      [
        'pattern' => 'bluesky/sync',
        'method' => 'POST',
        'action' => function () {
          // Force refetch - getBlueskyPosts(true) bypasses cache and overwrites it
          $notesPage = page('notes');
          if ($notesPage) {
            $notesPage->getBlueskyPosts(true);
          }

          return [
            'status' => 'success',
            'message' => 'Bluesky posts synced successfully'
          ];
        }
      ],
      [
        'pattern' => 'bluesky/status',
        'method' => 'GET',
        'action' => function () {
          $cache = kirby()->cache('bluesky');
          $data = $cache->get('posts');

          return [
            'cached' => $data !== null,
            'count' => $data !== null ? count($data) : 0,
            'lastSync' => $cache->get('lastSync')
          ];
        }
      ]
    ]
  ]
]);
