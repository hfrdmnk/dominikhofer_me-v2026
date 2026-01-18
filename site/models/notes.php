<?php

/**
 * Notes Page Model
 *
 * Extends the default page to merge filesystem children with
 * virtual Bluesky posts fetched from the API.
 */
class NotesPage extends Page
{
  /**
   * Override children to include virtual Bluesky posts
   */
  public function children(): Pages
  {
    // Get filesystem children
    $fileChildren = parent::children();

    // Get cached Bluesky posts as virtual pages
    $blueskyPages = $this->getBlueskyPosts();

    // Merge and return
    return $fileChildren->merge($blueskyPages);
  }

  /**
   * Get Bluesky posts as virtual Pages collection
   */
  public function getBlueskyPosts(bool $refresh = false): Pages
  {
    $cache = kirby()->cache('bluesky');

    // Check if we need to refresh
    $data = $refresh ? null : $cache->get('posts');

    if ($data === null) {
      $data = $this->fetchAndFilterBlueskyPosts();
      $cache->set('posts', $data, option('dominik.bluesky.cacheTtl', 3600));
      $cache->set('lastSync', date('c'), option('dominik.bluesky.cacheTtl', 3600));
    }

    // Convert cached data to Pages collection
    return Pages::factory($data, $this);
  }

  /**
   * Fetch posts from API and filter them
   */
  protected function fetchAndFilterBlueskyPosts(): array
  {
    $did = option('dominik.bluesky.did');

    if (empty($did)) {
      return [];
    }

    $posts = BlueskyApi::fetchPosts($did);
    return BlueskyParser::filterAndTransform($posts);
  }
}
