<?php

use Kirby\Cms\App as Kirby;

require_once __DIR__ . '/lib/OpmlParser.php';

Kirby::plugin('dominikhofer/opml', [
  'fileTypes' => [
    'opml' => [
      'mime' => ['text/xml', 'application/xml', 'text/x-opml'],
      'type' => 'document',
    ],
  ],
  'blueprints' => [
    'blocks/feedlist' => __DIR__ . '/blueprints/blocks/feedlist.yml',
  ],
  'snippets' => [
    'blocks/feedlist' => __DIR__ . '/snippets/blocks/feedlist.php',
  ],
]);
