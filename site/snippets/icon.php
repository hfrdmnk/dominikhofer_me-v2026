<?php
/**
 * Icon snippet - renders SVG icons from assets/icons/
 * @param string $name - Icon filename without extension
 * @param string $class - CSS classes
 */
$iconPath = kirby()->root('index') . '/assets/icons/' . $name . '.svg';

if (file_exists($iconPath)):
  $svg = file_get_contents($iconPath);
  // Only remove width/height from the root <svg> element
  $svg = preg_replace('/<svg([^>]*)\s+width="[^"]*"/', '<svg$1', $svg);
  $svg = preg_replace('/<svg([^>]*)\s+height="[^"]*"/', '<svg$1', $svg);
  // Replace fill on path elements with currentColor, but preserve fill="none" and fill="white"
  $svg = preg_replace('/(<path[^>]*)\s+fill="(?!none|white)[^"]*"/', '$1 fill="currentColor"', $svg);
  $svg = preg_replace('/<svg/', '<svg class="' . ($class ?? '') . '"', $svg);
  echo $svg;
endif;
