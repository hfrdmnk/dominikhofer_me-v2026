<?php

use Kirby\Cms\App;

App::plugin('dominik/rss-helpers', [
  'siteMethods' => [
    'absoluteUrls' => function (string $html): string {
      $baseUrl = rtrim($this->url(), '/');

      // Convert relative URLs in href and src attributes
      return preg_replace_callback(
        '/(href|src)=["\'](?!https?:\/\/|mailto:|tel:|#)([^"\']+)["\']/i',
        function ($matches) use ($baseUrl) {
          $attr = $matches[1];
          $url = $matches[2];

          // Ensure leading slash for relative URLs
          if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
          }

          return $attr . '="' . $baseUrl . $url . '"';
        },
        $html
      );
    }
  ]
]);
