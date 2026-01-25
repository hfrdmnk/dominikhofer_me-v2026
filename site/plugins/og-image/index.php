<?php

use Kirby\Cms\Response;

Kirby::plugin('dominik/og-image', [
  'routes' => [
    [
      'pattern' => '(:all).png',
      'action' => function ($slug) {
        $site = site();
        $kirby = kirby();

        // Determine which page this is for
        if (empty($slug) || $slug === 'home') {
          // Homepage
          $page = $site->homePage();
        } else {
          // Try direct page first
          $page = page($slug);

          // Try flat routing (content items in collection pages)
          if (!$page) {
            foreach (['posts', 'notes', 'photos', 'races'] as $parent) {
              if ($page = page($parent . '/' . $slug)) {
                break;
              }
            }
          }

          // Try tag pages
          if (!$page && str_starts_with($slug, 'tag/')) {
            $tag = urldecode(substr($slug, 4));
            $page = (object) ['id' => 'tag-' . $tag, 'modified' => time(), 'slug' => $tag, 'isTag' => true];
          }
        }

        if (!$page) {
          return $site->errorPage();
        }

        // Cache key based on page ID and modification time
        $isTag = is_object($page) && isset($page->isTag) && $page->isTag;
        $cacheKey = $isTag ? $page->id : 'og-' . str_replace('/', '-', $page->id()) . '-' . $page->modified();
        $cache = $kirby->cache('og-image');
        $cached = $cache->get($cacheKey);

        if ($cached) {
          return new Response($cached, 'image/png', 200, [
            'Cache-Control' => 'public, max-age=31536000'
          ]);
        }

        // Generate image
        $width = 1200;
        $height = 628;
        $bgColor = [245, 245, 241];
        $orangeColor = [230, 84, 10];
        $mutedColor = [181, 172, 160];
        $fontPath = $kirby->root('assets') . '/fonts/DepartureMono-Regular.otf';
        $fontSize = 28;

        // Create image
        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, $bgColor[0], $bgColor[1], $bgColor[2]);
        $orange = imagecolorallocate($img, $orangeColor[0], $orangeColor[1], $orangeColor[2]);
        $muted = imagecolorallocate($img, $mutedColor[0], $mutedColor[1], $mutedColor[2]);

        imagefill($img, 0, 0, $bg);

        // Determine text parts
        $domain = 'dominikhofer.me';
        $isHome = !$isTag && $page->id() === $site->homePage()->id();

        if ($isHome) {
          $orangeText = $domain;
          $mutedText = 'https://';
        } elseif ($isTag) {
          $orangeText = '/tag/' . $page->slug;
          $mutedText = 'https://' . $domain;
        } else {
          $pageSlug = $isTag ? $page->slug : $page->slug();
          $orangeText = '/' . $pageSlug;
          $mutedText = 'https://' . $domain;
        }

        // Calculate text dimensions
        $orangeBox = imagettfbbox($fontSize, 0, $fontPath, $orangeText);
        $orangeWidth = abs($orangeBox[2] - $orangeBox[0]);
        $orangeHeight = abs($orangeBox[7] - $orangeBox[1]);

        $mutedBox = imagettfbbox($fontSize, 0, $fontPath, $mutedText);
        $mutedWidth = abs($mutedBox[2] - $mutedBox[0]);

        // Center orange text horizontally
        $orangeX = ($width - $orangeWidth) / 2;
        $textY = ($height + $orangeHeight) / 2;

        // Position muted text immediately to left of orange text
        $mutedX = $orangeX - $mutedWidth - 10;

        // Draw texts
        imagettftext($img, $fontSize, 0, (int)$mutedX, (int)$textY, $muted, $fontPath, $mutedText);
        imagettftext($img, $fontSize, 0, (int)$orangeX, (int)$textY, $orange, $fontPath, $orangeText);

        // Output to buffer
        ob_start();
        imagepng($img);
        $imageData = ob_get_clean();
        imagedestroy($img);

        // Cache the result
        $cache->set($cacheKey, $imageData, 60 * 24 * 7); // 7 days

        return new Response($imageData, 'image/png', 200, [
          'Cache-Control' => 'public, max-age=31536000'
        ]);
      }
    ]
  ]
]);
