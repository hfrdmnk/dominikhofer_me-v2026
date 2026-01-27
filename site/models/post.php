<?php

class PostPage extends Page
{
  public function url($options = null): string
  {
    return site()->url() . '/' . $this->slug();
  }

  /**
   * Returns excerpt + hashtags for social media posting
   */
  public function socialText(): Kirby\Content\Field
  {
    $text = $this->excerpt()->value();

    // Append tags as hashtags
    if ($this->tags()->isNotEmpty()) {
      $hashtags = array_map(
        fn($tag) => '#' . preg_replace('/\s+/', '', $tag),
        $this->tags()->split(',')
      );
      $text .= "\n\n" . implode(' ', $hashtags);
    }

    return new Kirby\Content\Field($this, 'socialText', $text);
  }
}
