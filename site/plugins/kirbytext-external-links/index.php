<?php

use Kirby\Cms\App;

App::plugin('dominik/kirbytext-external-links', [
  'hooks' => [
    'kirbytext:after' => function (string $text): string {
      // Add target="_blank" rel="noopener" to external links
      return preg_replace_callback(
        '/<a\s+([^>]*href=["\'])(https?:\/\/[^"\']+)(["\'][^>]*)>/i',
        function ($matches) {
          $before = $matches[1];
          $url = $matches[2];
          $after = $matches[3];

          // Check if it's an external URL (not our domain)
          $siteHost = parse_url(site()->url(), PHP_URL_HOST);
          $linkHost = parse_url($url, PHP_URL_HOST);

          if ($linkHost && $linkHost !== $siteHost) {
            // Only add if not already present
            $attrs = $before . $url . $after;
            if (stripos($attrs, 'target=') === false) {
              return '<a ' . $before . $url . $after . ' target="_blank" rel="noopener">';
            }
          }
          return $matches[0];
        },
        $text
      );
    }
  ]
]);
