<?php

/**
 * RSS Feed Generator
 *
 * Generates RSS 2.0 XML feeds with full content for posts, notes, photos, and races.
 */
class RssFeed
{
  private $site;
  private $authorName;
  private $authorEmail;
  private $authorBio;

  public function __construct()
  {
    $this->site = site();
    $this->authorName = $this->site->author_name()->value();
    $this->authorBio = $this->site->author_bio()->value();
    $this->authorEmail = $this->getAuthorEmail();
  }

  /**
   * Get author email from social_links structure
   */
  private function getAuthorEmail(): string
  {
    $socialLinks = $this->site->social_links()->toStructure();
    $emailLink = $socialLinks->findBy('platform', 'email');

    if ($emailLink && $emailLink->url()->isNotEmpty()) {
      $url = $emailLink->url()->value();
      // Remove mailto: prefix if present
      return str_replace('mailto:', '', $url);
    }

    return '';
  }

  /**
   * Generate home feed with all content types
   */
  public function home(): Kirby\Cms\Response
  {
    $items = $this->site->index()->listed()
      ->filterBy('intendedTemplate', 'in', ['post', 'note', 'photo', 'race'])
      ->sortBy('date', 'desc');

    return $this->generate(
      title: 'All Posts',
      description: $this->authorBio,
      link: $this->site->url(),
      feedUrl: $this->site->url() . '/rss',
      items: $items
    );
  }

  /**
   * Generate section feed
   */
  public function section(string $section): Kirby\Cms\Response
  {
    $page = page($section);
    if (!$page) {
      return new Kirby\Cms\Response('Not found', 'text/plain', 404);
    }

    $items = $page->children()->listed()->sortBy('date', 'desc');

    $titles = [
      'posts' => 'Posts',
      'notes' => 'Notes',
      'photos' => 'Photos',
      'races' => 'Races',
    ];

    return $this->generate(
      title: $titles[$section] ?? ucfirst($section),
      description: $this->authorBio,
      link: $page->url(),
      feedUrl: $page->url() . '/rss',
      items: $items
    );
  }

  /**
   * Generate RSS XML
   */
  private function generate(
    string $title,
    string $description,
    string $link,
    string $feedUrl,
    $items
  ): Kirby\Cms\Response {
    $lastBuildDate = $items->first()
      ? date('r', $items->first()->date()->toTimestamp())
      : date('r');

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<?xml-stylesheet href="/assets/rss/rss.xsl" type="text/xsl"?>' . "\n";
    $xml .= '<rss version="2.0"' . "\n";
    $xml .= '     xmlns:content="http://purl.org/rss/1.0/modules/content/"' . "\n";
    $xml .= '     xmlns:media="http://search.yahoo.com/mrss/"' . "\n";
    $xml .= '     xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
    $xml .= '  <channel>' . "\n";
    $xml .= '    <title>' . $this->escape($title . ' | ' . $this->authorName) . '</title>' . "\n";
    $xml .= '    <link>' . $this->escape($link) . '</link>' . "\n";
    $xml .= '    <atom:link href="' . $this->escape($feedUrl) . '" rel="self" type="application/rss+xml"/>' . "\n";
    $xml .= '    <description>' . $this->escape($description) . '</description>' . "\n";
    $xml .= '    <language>en</language>' . "\n";
    $xml .= '    <lastBuildDate>' . $lastBuildDate . '</lastBuildDate>' . "\n";

    foreach ($items as $item) {
      $xml .= $this->generateItem($item);
    }

    $xml .= '  </channel>' . "\n";
    $xml .= '</rss>';

    return new Kirby\Cms\Response($xml, 'application/xml', 200, [
      'Content-Type' => 'application/xml; charset=utf-8',
      'X-Content-Type-Options' => 'nosniff'
    ]);
  }

  /**
   * Generate a single RSS item
   */
  private function generateItem($item): string
  {
    $template = $item->intendedTemplate()->name();
    $title = $this->getItemTitle($item, $template);
    $link = $item->url();
    $pubDate = date('r', $item->date()->toTimestamp());
    $description = $this->getItemDescription($item, $template);
    $content = $this->getItemContent($item, $template);
    $author = $this->authorEmail . ' (' . $this->authorName . ')';

    $xml = '    <item>' . "\n";
    $xml .= '      <title>' . $this->escape($title) . '</title>' . "\n";
    $xml .= '      <link>' . $this->escape($link) . '</link>' . "\n";
    $xml .= '      <guid isPermaLink="true">' . $this->escape($link) . '</guid>' . "\n";
    $xml .= '      <pubDate>' . $pubDate . '</pubDate>' . "\n";
    $xml .= '      <description>' . $this->escape($description) . '</description>' . "\n";
    $xml .= '      <content:encoded><![CDATA[' . $content . ']]></content:encoded>' . "\n";
    $xml .= '      <author>' . $this->escape($author) . '</author>' . "\n";

    // Add media:content for items with images
    $mediaUrl = $this->getMediaUrl($item, $template);
    if ($mediaUrl) {
      $xml .= '      <media:content url="' . $this->escape($mediaUrl) . '" type="image/webp" medium="image"/>' . "\n";
    }

    $xml .= '    </item>' . "\n";

    return $xml;
  }

  /**
   * Get item title based on content type
   */
  private function getItemTitle($item, string $template): string
  {
    return match ($template) {
      'post', 'race' => $item->title()->value(),
      'note' => 'Note',
      'photo' => $item->location()->isNotEmpty()
        ? 'Photo: ' . $item->location()->value()
        : 'Photo',
      default => $item->title()->value(),
    };
  }

  /**
   * Get item description/excerpt
   */
  private function getItemDescription($item, string $template): string
  {
    return match ($template) {
      'post' => $item->excerpt()->isNotEmpty()
        ? $item->excerpt()->value()
        : Str::short(strip_tags($item->body()->kirbytext()), 200),
      'note' => Str::short(strip_tags($item->body()->kirbytext()), 200),
      'photo' => $item->body()->isNotEmpty()
        ? Str::short(strip_tags($item->body()->kirbytext()), 200)
        : ($item->location()->isNotEmpty() ? $item->location()->value() : 'Photo'),
      'race' => $item->distance()->value() . 'km in ' . $item->time()->value(),
      default => '',
    };
  }

  /**
   * Get full HTML content using snippets
   */
  private function getItemContent($item, string $template): string
  {
    return snippet('rss/' . $template, [
      'item' => $item,
      'site' => $this->site,
      'authorEmail' => $this->authorEmail,
      'authorName' => $this->authorName,
    ], true);
  }

  /**
   * Get media URL for the item
   */
  private function getMediaUrl($item, string $template): ?string
  {
    return match ($template) {
      'post' => $item->cover()->toFile()?->url(),
      'photo' => $item->files()->first()?->url(),
      default => null,
    };
  }

  /**
   * Escape XML special characters
   */
  private function escape(string $text): string
  {
    return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
  }
}
