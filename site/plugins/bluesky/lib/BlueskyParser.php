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

    // Match lines that are just a URL
    if (preg_match_all('/^(https?:\/\/[^\s]+)$/m', $text, $matches)) {
      foreach ($matches[1] as $url) {
        $host = parse_url($url, PHP_URL_HOST);
        if ($host === null) continue;

        // Remove www. prefix for comparison
        $host = preg_replace('/^www\./', '', $host);

        // Check if the host is not in excluded domains
        $excluded = false;
        foreach ($excludeDomains as $domain) {
          if ($host === $domain || str_ends_with($host, '.' . $domain)) {
            $excluded = true;
            break;
          }
        }

        if (!$excluded) {
          return true;
        }
      }
    }

    return false;
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
      'tags' => $tags
    ];
  }

  /**
   * Check if post should be imported (matches filter criteria)
   */
  public static function shouldImport(array $post): bool
  {
    // Skip replies (posts that are in reply to someone else)
    if (isset($post['record']['reply'])) {
      return false;
    }

    // Skip reposts
    if (isset($post['record']['$type']) && $post['record']['$type'] === 'app.bsky.feed.repost') {
      return false;
    }

    return self::isLinkPost($post) || self::hasBuildinpublicTag($post);
  }

  /**
   * Transform a Bluesky post to Kirby page data
   */
  public static function toPageData(array $post): array
  {
    $rkey = BlueskyApi::extractRkey($post['uri']);
    $createdAt = $post['record']['createdAt'] ?? $post['indexedAt'] ?? date('c');
    $date = new DateTime($createdAt);

    // Extract text and trailing hashtags
    $text = $post['record']['text'] ?? '';
    $extracted = self::extractTrailingHashtags($text);

    // Build tags array: always include 'bluesky', plus extracted hashtags
    $tags = array_merge(['bluesky'], $extracted['tags']);
    $tags = array_unique($tags);

    // Extract media URLs from embed
    $mediaUrls = self::extractMediaUrls($post);

    return [
      'slug' => $rkey,
      'num' => (int) $date->format('Ymd'),
      'template' => 'note',
      'model' => 'note',
      'content' => [
        'title' => $rkey,
        'date' => $date->format('Y-m-d H:i:s'),
        'tags' => implode(', ', $tags),
        'body' => $extracted['text'],
        'uuid' => 'bluesky://' . $rkey,
        'media_urls' => implode(', ', $mediaUrls),
      ]
    ];
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

    // Handle video
    if (isset($embed['video']['playlist'])) {
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

    return $urls;
  }

  /**
   * Filter and transform posts for import
   */
  public static function filterAndTransform(array $posts): array
  {
    $result = [];

    foreach ($posts as $post) {
      if (self::shouldImport($post)) {
        $result[] = self::toPageData($post);
      }
    }

    return $result;
  }
}
