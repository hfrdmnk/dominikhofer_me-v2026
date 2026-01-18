<?php

/**
 * Bluesky API Client
 *
 * Fetches posts from the public Bluesky API with pagination support.
 */
class BlueskyApi
{
  const API_BASE = 'https://public.api.bsky.app/xrpc';

  /**
   * Fetch all posts for a given DID with pagination
   */
  public static function fetchPosts(string $did): array
  {
    $allPosts = [];
    $cursor = null;
    $limit = 100;

    do {
      $url = self::API_BASE . '/app.bsky.feed.getAuthorFeed?' . http_build_query([
        'actor' => $did,
        'filter' => 'posts_and_author_threads',
        'limit' => $limit,
      ]);

      if ($cursor) {
        $url .= '&cursor=' . urlencode($cursor);
      }

      $response = self::makeRequest($url);

      if ($response === null) {
        break;
      }

      if (isset($response['feed'])) {
        foreach ($response['feed'] as $feedItem) {
          if (isset($feedItem['post'])) {
            $allPosts[] = $feedItem['post'];
          }
        }
      }

      $cursor = $response['cursor'] ?? null;

    } while ($cursor !== null);

    return $allPosts;
  }

  /**
   * Make HTTP request to the API
   */
  private static function makeRequest(string $url): ?array
  {
    $context = stream_context_create([
      'http' => [
        'method' => 'GET',
        'header' => [
          'Accept: application/json',
          'User-Agent: DominikHofer-Website/1.0'
        ],
        'timeout' => 30,
        'ignore_errors' => true
      ],
      'ssl' => [
        'verify_peer' => true,
        'verify_peer_name' => true
      ]
    ]);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
      return null;
    }

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
      return null;
    }

    return $data;
  }

  /**
   * Extract rkey from AT-URI
   * Format: at://did:plc:xxx/app.bsky.feed.post/rkey
   */
  public static function extractRkey(string $atUri): ?string
  {
    $parts = explode('/', $atUri);
    return end($parts) ?: null;
  }
}
