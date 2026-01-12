<?php

return [
  'debug' => false,

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
    }
  ],

  // Flat URL routing for nested content
  'routes' => [
    // Route listing pages (posts, notes, photos, races) to their nested location
    [
      'pattern' => '(posts|notes|photos|races)',
      'action'  => function ($slug) {
        if ($page = page('home/' . $slug)) {
          return site()->visit($page);
        }
        return site()->errorPage();
      }
    ],
    // Route individual content items to flat URLs
    [
      'pattern' => '(:any)',
      'action'  => function ($slug) {
        // Try direct pages first (about, now, slash, home, etc.)
        if ($page = page($slug)) {
          return $page;
        }
        // Try nested content types under home
        foreach (['posts', 'notes', 'photos', 'races'] as $parent) {
          if ($page = page('home/' . $parent . '/' . $slug)) {
            return site()->visit($page);
          }
        }
        // Not found
        return site()->errorPage();
      }
    ]
  ]
];
