<?php

/**
 * Bluesky Post Parser
 *
 * Handles filtering posts and extracting hashtags.
 */
class BlueskyParser
{
  /**
   * Check if post has a standalone link on its own line
   * Excludes bsky.app and dominikhofer.me links
   */
  public static function isLinkPost(array $post): bool
  {
    $text = $post['record']['text'] ?? '';
    $excludeDomains = option('dominik.bluesky.excludeDomains', ['bsky.app', 'dominikhofer.me']);

    // Check 1: Lines in text that are full URLs (https://...)
    if (preg_match_all('/^(https?:\/\/[^\s]+)$/m', $text, $matches)) {
      foreach ($matches[1] as $url) {
        $host = parse_url($url, PHP_URL_HOST);
        if ($host && self::isAllowedDomain($host, $excludeDomains)) {
          return true;
        }
      }
    }

    // Check 2: External embed where domain appears on its own line
    $embed = $post['embed'] ?? null;
    if ($embed && isset($embed['external']['uri'])) {
      $embedHost = parse_url($embed['external']['uri'], PHP_URL_HOST);
      if ($embedHost) {
        $embedHost = preg_replace('/^www\./', '', $embedHost);

        // Check if any line starts with this domain
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
          $line = trim($line);
          // Normalize line by stripping www. (same as embedHost)
          $normalizedLine = preg_replace('/^www\./', '', $line);
          // Match: domain/path or domain (line is just the domain with optional path)
          if (preg_match('/^' . preg_quote($embedHost, '/') . '(\/\S*)?$/', $normalizedLine)) {
            if (self::isAllowedDomain($embedHost, $excludeDomains)) {
              return true;
            }
          }
        }
      }
    }

    return false;
  }

  /**
   * Check if domain is not in excluded list
   */
  private static function isAllowedDomain(string $host, array $excludeDomains): bool
  {
    $host = preg_replace('/^www\./', '', $host);

    foreach ($excludeDomains as $domain) {
      if ($host === $domain || str_ends_with($host, '.' . $domain)) {
        return false;
      }
    }

    return true;
  }

  /**
   * Check if post has #buildinpublic hashtag
   */
  public static function hasBuildinpublicTag(array $post): bool
  {
    $text = $post['record']['text'] ?? '';
    return preg_match('/#buildinpublic\b/i', $text) === 1;
  }

  /**
   * Extract trailing hashtags from text
   * Returns: ['text' => 'cleaned text', 'tags' => ['tag1', 'tag2']]
   */
  public static function extractTrailingHashtags(string $text): array
  {
    $tags = [];

    // Match trailing hashtags (at end of text, separated by whitespace/newlines)
    if (preg_match('/(\s*(#\w+))+\s*$/', $text, $matches, PREG_OFFSET_CAPTURE)) {
      $trailingPart = $matches[0][0];
      $offset = $matches[0][1];

      // Extract individual hashtags
      preg_match_all('/#(\w+)/', $trailingPart, $tagMatches);
      $tags = array_map('strtolower', $tagMatches[1]);

      // Remove trailing hashtags from text
      $text = rtrim(substr($text, 0, $offset));
    }

    return [
      'text' => $text,
      'tags' => $tags,
      'trailingStart' => $offset ?? strlen($text)
    ];
  }

  /**
   * Apply facets (links, mentions, hashtags) to text
   * Returns HTML-formatted text with clickable links
   *
   * @param string $text The original post text
   * @param array $facets The facets array from the post record
   * @param int $trailingHashtagStart Byte offset where trailing hashtags begin (skip hashtag facets after this)
   */
  public static function applyFacets(string $text, array $facets, int $trailingHashtagStart): string
  {
    if (empty($facets)) {
      return $text;
    }

    // Sort facets by byteStart descending (process from end to avoid offset shifts)
    usort($facets, function ($a, $b) {
      return ($b['index']['byteStart'] ?? 0) - ($a['index']['byteStart'] ?? 0);
    });

    foreach ($facets as $facet) {
      $byteStart = $facet['index']['byteStart'] ?? null;
      $byteEnd = $facet['index']['byteEnd'] ?? null;
      $features = $facet['features'] ?? [];

      // Validate bounds
      if ($byteStart === null || $byteEnd === null) {
        continue;
      }
      if ($byteStart >= strlen($text) || $byteEnd > strlen($text)) {
        continue;
      }
      if ($byteStart < 0 || $byteEnd <= $byteStart) {
        continue;
      }

      // Get the first feature (multiple features per facet is rare)
      $feature = $features[0] ?? null;
      if (!$feature) {
        continue;
      }

      $type = $feature['$type'] ?? '';
      $segment = substr($text, $byteStart, $byteEnd - $byteStart);

      // Skip trailing hashtag facets (they become page tags, not inline links)
      if ($type === 'app.bsky.richtext.facet#tag' && $byteStart >= $trailingHashtagStart) {
        continue;
      }

      $replacement = null;

      switch ($type) {
        case 'app.bsky.richtext.facet#link':
          $uri = $feature['uri'] ?? '';
          if ($uri) {
            // Check if this is a YouTube URL - render as Kirby video tag
            if (self::isYouTubeUrl($uri)) {
              $replacement = "\n\n(video: " . $uri . ")\n\n";
            } else {
              $replacement = '<a href="' . htmlspecialchars($uri, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' .
                            htmlspecialchars($segment, ENT_QUOTES, 'UTF-8') . '</a>';
            }
          }
          break;

        case 'app.bsky.richtext.facet#mention':
          $did = $feature['did'] ?? '';
          if ($did) {
            $replacement = '<a href="https://bsky.app/profile/' . htmlspecialchars($did, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' .
                          htmlspecialchars($segment, ENT_QUOTES, 'UTF-8') . '</a>';
          }
          break;

        case 'app.bsky.richtext.facet#tag':
          $tag = $feature['tag'] ?? '';
          if ($tag) {
            $replacement = '<a href="https://bsky.app/hashtag/' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">' .
                          htmlspecialchars($segment, ENT_QUOTES, 'UTF-8') . '</a>';
          }
          break;
      }

      if ($replacement !== null) {
        $text = substr($text, 0, $byteStart) . $replacement . substr($text, $byteEnd);
      }
    }

    return $text;
  }

  /**
   * Check if this is a repost (by checking feed item reason)
   */
  public static function isRepost(array $feedItem): bool
  {
    return isset($feedItem['reason']['$type']) &&
           $feedItem['reason']['$type'] === 'app.bsky.feed.defs#reasonRepost';
  }

  /**
   * Check if post is a quote post (embedding another post)
   */
  public static function isQuotePost(array $post): bool
  {
    $embedType = $post['embed']['$type'] ?? '';
    return $embedType === 'app.bsky.embed.record#view' ||
           $embedType === 'app.bsky.embed.recordWithMedia#view';
  }

  /**
   * Extract quoted post info from a quote post
   */
  public static function extractQuotedPost(array $post): ?array
  {
    $embed = $post['embed'] ?? null;
    if (!$embed) return null;

    // Handle both record and recordWithMedia types
    $record = $embed['record']['record'] ?? $embed['record'] ?? null;
    if (!$record || !isset($record['author'])) return null;

    // Check for external embed on quoted record
    $externalUri = '';
    $embeds = $record['embeds'] ?? [];
    foreach ($embeds as $recordEmbed) {
      if (isset($recordEmbed['external']['uri'])) {
        $externalUri = $recordEmbed['external']['uri'];
        break;
      }
    }

    return [
      'author_handle' => $record['author']['handle'] ?? '',
      'author_did' => $record['author']['did'] ?? '',
      'author_name' => $record['author']['displayName'] ?? $record['author']['handle'] ?? '',
      'author_avatar' => $record['author']['avatar'] ?? '',
      'text' => $record['value']['text'] ?? '',
      'uri' => $record['uri'] ?? '',
      'external_uri' => $externalUri,
    ];
  }

  /**
   * Check if URL is a YouTube URL
   */
  private static function isYouTubeUrl(string $url): bool
  {
    return (bool) preg_match('/youtu\.?be/i', $url);
  }

  /**
   * Check if feed item should be imported (matches filter criteria)
   * Accepts full feed item (with 'post', 'reason', 'reply' fields)
   */
  public static function shouldImport(array $feedItem): bool
  {
    $post = $feedItem['post'] ?? $feedItem;

    // Skip replies (posts that are replies to someone else, unless it's a repost)
    // For reposts, we import based on the original post's criteria
    if (!self::isRepost($feedItem) && isset($post['record']['reply'])) {
      return false;
    }

    // Check standard criteria
    if (self::isLinkPost($post) || self::hasBuildinpublicTag($post)) {
      return true;
    }

    // Check quote posts for link content
    if (self::isQuotePost($post)) {
      $quotedPost = self::extractQuotedPost($post);
      if ($quotedPost) {
        $quotedText = $quotedPost['text'];
        $excludeDomains = option('dominik.bluesky.excludeDomains', ['bsky.app', 'dominikhofer.me']);

        // Check 1: Standalone links in quoted text
        if (preg_match_all('/^(https?:\/\/[^\s]+)$/m', $quotedText, $matches)) {
          foreach ($matches[1] as $url) {
            $host = parse_url($url, PHP_URL_HOST);
            if ($host && self::isAllowedDomain($host, $excludeDomains)) {
              return true;
            }
          }
        }

        // Check 2: External embed on quoted record
        if (!empty($quotedPost['external_uri'])) {
          $host = parse_url($quotedPost['external_uri'], PHP_URL_HOST);
          if ($host && self::isAllowedDomain($host, $excludeDomains)) {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * Transform a Bluesky feed item to Kirby page data
   * Accepts full feed item (with 'post', 'reason', 'reply' fields)
   */
  public static function toPageData(array $feedItem): array
  {
    $post = $feedItem['post'] ?? $feedItem;
    $isRepost = self::isRepost($feedItem);
    $isQuote = self::isQuotePost($post);

    $rkey = BlueskyApi::extractRkey($post['uri']);
    $createdAt = $post['record']['createdAt'] ?? $post['indexedAt'] ?? date('c');
    $date = new DateTime($createdAt);

    // Extract text and trailing hashtags
    $text = $post['record']['text'] ?? '';
    $extracted = self::extractTrailingHashtags($text);

    // Apply facets (links, mentions, inline hashtags) to the text
    $facets = $post['record']['facets'] ?? [];
    $bodyText = self::applyFacets($text, $facets, $extracted['trailingStart']);

    // Remove trailing hashtags from the facet-applied text
    // We need to strip them since they're converted to tags
    $bodyText = self::stripTrailingHashtags($bodyText, $extracted['tags']);

    // Add repost prefix if this is a repost (show original author, not reposter)
    if ($isRepost) {
      $originalAuthor = $post['author'] ?? [];
      $handle = $originalAuthor['handle'] ?? '';
      $did = $originalAuthor['did'] ?? '';
      if ($handle && $did) {
        $repostPrefix = '<span class="text-muted">Reposting</span> <a href="https://bsky.app/profile/' .
                       htmlspecialchars($did, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">@' .
                       htmlspecialchars($handle, ENT_QUOTES, 'UTF-8') . '</a>' . "\n\n";
        $bodyText = $repostPrefix . $bodyText;
      }
    }

    // Extract quoted post data if this is a quote post
    $quotedPost = null;
    if ($isQuote) {
      $quotedPost = self::extractQuotedPost($post);
      if ($quotedPost) {
        $handle = $quotedPost['author_handle'];
        $did = $quotedPost['author_did'];
        $quotePrefix = '<span class="text-muted">Quoting</span> <a href="https://bsky.app/profile/' .
                      htmlspecialchars($did, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">@' .
                      htmlspecialchars($handle, ENT_QUOTES, 'UTF-8') . '</a>' . "\n\n";
        $bodyText = $quotePrefix . $bodyText;
      }
    }

    // Build tags array: always include 'bluesky', plus extracted hashtags
    $tags = array_merge(['bluesky'], $extracted['tags']);

    // Add 'link' tag if this is a link post
    if (self::isLinkPost($post)) {
      $tags[] = 'link';
    }

    $tags = array_unique($tags);

    // Extract media URLs from embed
    $mediaUrls = self::extractMediaUrls($post);

    // Extract external link embed data
    $externalEmbed = self::extractExternalEmbed($post);

    return [
      'slug' => $rkey,
      'num' => (int) $date->format('Ymd'),
      'template' => 'note',
      'model' => 'note',
      'content' => [
        'title' => $rkey,
        'date' => $date->format('Y-m-d H:i:s'),
        'tags' => implode(', ', $tags),
        'body' => $bodyText,
        'uuid' => 'bluesky://' . $rkey,
        'media_urls' => implode(', ', $mediaUrls),
        'quoted_post' => $quotedPost ? json_encode($quotedPost) : '',
        'external_link' => $externalEmbed ? json_encode($externalEmbed) : '',
      ]
    ];
  }

  /**
   * Strip trailing hashtags from text (after facets have been applied)
   */
  private static function stripTrailingHashtags(string $text, array $tagsToRemove): string
  {
    if (empty($tagsToRemove)) {
      return $text;
    }

    // Build pattern to match trailing hashtags (with or without links)
    // This handles both plain hashtags and linked hashtags like <a href="...">hashtag</a>
    $pattern = '/(\s*(?:<a[^>]*>)?#(' . implode('|', array_map('preg_quote', $tagsToRemove)) . ')(?:<\/a>)?)+\s*$/i';
    return preg_replace($pattern, '', $text);
  }

  /**
   * Extract media URLs from post embed
   */
  private static function extractMediaUrls(array $post): array
  {
    $urls = [];

    $embed = $post['embed'] ?? null;
    if (!$embed) {
      return $urls;
    }

    // Handle images
    if (isset($embed['images'])) {
      foreach ($embed['images'] as $image) {
        if (isset($image['fullsize'])) {
          $urls[] = $image['fullsize'];
        }
      }
    }

    // Handle video - check both structures
    if (isset($embed['playlist'])) {
      // Feed view: playlist is directly on embed
      $urls[] = $embed['playlist'];
    } elseif (isset($embed['video']['playlist'])) {
      // Thread view: playlist nested under video
      $urls[] = $embed['video']['playlist'];
    }

    // Handle recordWithMedia (quote post with media)
    if (isset($embed['media']['images'])) {
      foreach ($embed['media']['images'] as $image) {
        if (isset($image['fullsize'])) {
          $urls[] = $image['fullsize'];
        }
      }
    }

    // Handle recordWithMedia video
    if (isset($embed['media']['playlist'])) {
      $urls[] = $embed['media']['playlist'];
    }

    return $urls;
  }

  /**
   * Extract external link embed data (OG cards)
   */
  private static function extractExternalEmbed(array $post): ?array
  {
    $embed = $post['embed'] ?? null;
    if (!$embed) return null;

    $external = $embed['external'] ?? null;
    if (!$external || !isset($external['uri'])) return null;

    // Skip YouTube links - they're already embedded via facets
    if (self::isYouTubeUrl($external['uri'])) {
      return null;
    }

    return [
      'uri' => $external['uri'] ?? '',
      'title' => $external['title'] ?? '',
      'description' => $external['description'] ?? '',
      'thumb' => $external['thumb'] ?? '',
    ];
  }

  /**
   * Filter and transform feed items for import, with thread support
   */
  public static function filterAndTransform(array $feedItems): array
  {
    $result = [];
    $threads = [];
    $processedRoots = [];

    // Get user's own DID for filtering self-replies
    $userDid = option('dominik.bluesky.did', '');

    // First pass: group posts by thread root
    foreach ($feedItems as $feedItem) {
      $post = $feedItem['post'] ?? $feedItem;
      $postUri = $post['uri'] ?? '';
      $authorDid = $post['author']['did'] ?? '';

      // Determine thread root URI
      $replyRoot = $post['record']['reply']['root']['uri'] ?? null;
      $rootUri = $replyRoot ?? $postUri;

      // For reposts, treat them as standalone (don't group into threads)
      if (self::isRepost($feedItem)) {
        if (self::shouldImport($feedItem)) {
          $result[] = self::toPageData($feedItem);
        }
        continue;
      }

      // Only consider replies to self (author threads)
      // Skip replies to other users
      if (isset($post['record']['reply'])) {
        $parentAuthor = $post['record']['reply']['parent']['uri'] ?? '';
        // If the parent is not from the same author, skip
        if (!str_contains($parentAuthor, $authorDid)) {
          continue;
        }
      }

      // Group by root URI
      if (!isset($threads[$rootUri])) {
        $threads[$rootUri] = [];
      }
      $threads[$rootUri][] = $feedItem;
    }

    // Second pass: process threads
    foreach ($threads as $rootUri => $threadItems) {
      // Sort by createdAt ascending
      usort($threadItems, function ($a, $b) {
        $aDate = $a['post']['record']['createdAt'] ?? $a['post']['indexedAt'] ?? '';
        $bDate = $b['post']['record']['createdAt'] ?? $b['post']['indexedAt'] ?? '';
        return strcmp($aDate, $bDate);
      });

      // Find root post (first in sorted order, or specifically the one matching rootUri)
      $rootItem = null;
      foreach ($threadItems as $item) {
        if (($item['post']['uri'] ?? '') === $rootUri) {
          $rootItem = $item;
          break;
        }
      }
      // Fallback to first item if root not found
      if (!$rootItem) {
        $rootItem = $threadItems[0];
      }

      // Only import if root post matches filter criteria
      if (!self::shouldImport($rootItem)) {
        continue;
      }

      // Single post thread - use simple toPageData
      if (count($threadItems) === 1) {
        $result[] = self::toPageData($threadItems[0]);
        continue;
      }

      // Multi-post thread - combine into single page
      $result[] = self::combineThread($threadItems, $rootItem);
    }

    return $result;
  }

  /**
   * Combine multiple thread posts into a single page
   */
  private static function combineThread(array $threadItems, array $rootItem): array
  {
    $bodies = [];
    $allMediaUrls = [];
    $allTags = ['bluesky'];
    $externalEmbed = null;
    $quotedPost = null;

    foreach ($threadItems as $feedItem) {
      $post = $feedItem['post'] ?? $feedItem;

      // Extract text and trailing hashtags
      $text = $post['record']['text'] ?? '';
      $extracted = self::extractTrailingHashtags($text);

      // Apply facets
      $facets = $post['record']['facets'] ?? [];
      $bodyText = self::applyFacets($text, $facets, $extracted['trailingStart']);
      $bodyText = self::stripTrailingHashtags($bodyText, $extracted['tags']);

      $bodies[] = $bodyText;

      // Collect tags
      $allTags = array_merge($allTags, $extracted['tags']);

      // Collect media URLs
      $mediaUrls = self::extractMediaUrls($post);
      $allMediaUrls = array_merge($allMediaUrls, $mediaUrls);

      // Collect external embed from first post that has one
      if (!$externalEmbed) {
        $externalEmbed = self::extractExternalEmbed($post);
      }

      // Collect quoted post from first post that has one
      if (!$quotedPost && self::isQuotePost($post)) {
        $quotedPost = self::extractQuotedPost($post);
      }
    }

    $rootPost = $rootItem['post'] ?? $rootItem;
    $rkey = BlueskyApi::extractRkey($rootPost['uri']);
    $createdAt = $rootPost['record']['createdAt'] ?? $rootPost['indexedAt'] ?? date('c');
    $date = new DateTime($createdAt);

    // Check root post for link criteria
    if (self::isLinkPost($rootPost)) {
      $allTags[] = 'link';
    }

    $allTags = array_unique($allTags);
    $allMediaUrls = array_unique($allMediaUrls);

    return [
      'slug' => $rkey,
      'num' => (int) $date->format('Ymd'),
      'template' => 'note',
      'model' => 'note',
      'content' => [
        'title' => $rkey,
        'date' => $date->format('Y-m-d H:i:s'),
        'tags' => implode(', ', $allTags),
        'body' => implode("\n\n---\n\n", $bodies),
        'uuid' => 'bluesky://' . $rkey,
        'media_urls' => implode(', ', $allMediaUrls),
        'thread_count' => count($threadItems),
        'quoted_post' => $quotedPost ? json_encode($quotedPost) : '',
        'external_link' => $externalEmbed ? json_encode($externalEmbed) : '',
      ]
    ];
  }
}
