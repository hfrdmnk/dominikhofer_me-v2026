<?php

namespace Opml;

class OpmlParser
{
  /**
   * Parse an OPML file and return all sections with their feeds
   */
  public static function parse(string $filePath): array
  {
    if (!file_exists($filePath)) {
      return [];
    }

    $xml = simplexml_load_file($filePath);
    if ($xml === false) {
      return [];
    }

    $sections = [];

    foreach ($xml->body->outline as $outline) {
      $section = self::parseOutline($outline);
      if ($section) {
        $sections[] = $section;
      }
    }

    return $sections;
  }

  /**
   * Parse an outline element (section or feed)
   */
  private static function parseOutline(\SimpleXMLElement $outline): ?array
  {
    $attrs = $outline->attributes();
    $text = (string)($attrs['text'] ?? $attrs['title'] ?? '');

    // Check if this is a feed (has xmlUrl) or a section (has children)
    if (isset($attrs['xmlUrl'])) {
      // This is a feed
      return [
        'type' => 'feed',
        'title' => $text,
        'xmlUrl' => (string)$attrs['xmlUrl'],
        'htmlUrl' => (string)($attrs['htmlUrl'] ?? ''),
      ];
    }

    // This is a section/folder
    $feeds = [];
    foreach ($outline->outline as $child) {
      $feed = self::parseOutline($child);
      if ($feed && $feed['type'] === 'feed') {
        $feeds[] = $feed;
      }
    }

    if (empty($text) && empty($feeds)) {
      return null;
    }

    return [
      'type' => 'section',
      'title' => $text,
      'feeds' => $feeds,
    ];
  }
}
