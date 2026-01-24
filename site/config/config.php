<?php

return [
  'debug' => false,

  // Bluesky integration
  'dominik.bluesky' => [
    'did' => 'did:plc:fthx2gjakdj4ynxxu5vysjty',
    'cacheTtl' => 60 * 60, // 1 hour
    'excludeDomains' => ['bsky.app', 'dominikhofer.me'],
  ],

  // Enable Bluesky cache
  'cache' => [
    'bluesky' => true
  ],

  // Panel sidebar menu with quick access to content types
  'panel' => [
    'menu' => [
      'site',
      '-',
      'posts' => [
        'label' => 'Posts',
        'link'  => 'pages/posts',
        'icon'  => 'text'
      ],
      'notes' => [
        'label' => 'Notes',
        'link'  => 'pages/notes',
        'icon'  => 'chat'
      ],
      'photos' => [
        'label' => 'Photos',
        'link'  => 'pages/photos',
        'icon'  => 'image'
      ],
      'races' => [
        'label' => 'Races',
        'link'  => 'pages/races',
        'icon'  => 'bolt'
      ],
      '-',
      'users',
      'system'
    ]
  ],

  // Enable Markdown Extra for footnotes support
  'markdown' => [
    'extra' => true
  ],

  // Image processing: Replace originals on upload (2160px max, 80% quality, WebP)
  'hooks' => [
    'file.create:after' => function ($file) {
      if ($file->isResizable() && $file->width() > 2160) {
        kirby()->thumb($file->root(), $file->root(), [
          'width'   => 2160,
          'quality' => 80,
          'format'  => 'webp'
        ]);
      }
    },
    'file.replace:after' => function ($newFile, $oldFile) {
      if ($newFile->isResizable() && $newFile->width() > 2160) {
        kirby()->thumb($newFile->root(), $newFile->root(), [
          'width'   => 2160,
          'quality' => 80,
          'format'  => 'webp'
        ]);
      }
    },

    // Update folder prefix when date changes on listed pages
    'page.update:after' => function ($newPage, $oldPage) {
      if ($newPage->status() !== 'listed') {
        return;
      }

      $template = $newPage->intendedTemplate()->name();
      if (!in_array($template, ['post', 'note', 'photo', 'race'])) {
        return;
      }

      $newDate = $newPage->date()->toDate();
      $oldDate = $oldPage->date()->toDate();

      if ($newDate !== $oldDate) {
        $newNum = (int) date('Ymd', $newDate);
        if ($newPage->num() !== $newNum) {
          $newPage->changeNum($newNum);
        }
      }
    }
  ],

  // Flat URL routing for content items
  'routes' => [
    // Tag archive route
    [
      'pattern' => 'tag/(:any)',
      'action'  => function ($tag) {
        $tag = urldecode($tag);

        $items = site()->index()
          ->listed()
          ->filterBy('intendedTemplate', 'in', ['post', 'note', 'photo', 'race'])
          ->filterBy('tags', $tag, ',')
          ->sortBy('date', 'desc');

        if ($items->isEmpty()) {
          return site()->errorPage();
        }

        return Page::factory([
          'slug' => 'tag',
          'template' => 'tag',
          'content' => [
            'title' => '#' . $tag,
            'tag' => $tag
          ]
        ]);
      }
    ],
    // Route individual content items to flat URLs
    [
      'pattern' => '(:any)',
      'action'  => function ($slug) {
        // Try direct pages first (about, now, slash, home, posts, notes, etc.)
        if ($page = page($slug)) {
          return $page;
        }
        // Try content types as children of collection pages
        foreach (['posts', 'notes', 'photos', 'races'] as $parent) {
          if ($page = page($parent . '/' . $slug)) {
            return site()->visit($page);
          }
        }
        // Not found
        return site()->errorPage();
      }
    ]
  ]
];
