<?php
/**
 * Icon snippet - renders SVG icons from assets/icons/
 * @param string $name - Icon filename without extension
 * @param string $class - CSS classes
 */
$iconPath = kirby()->root('index') . '/assets/icons/' . $name . '.svg';

if (file_exists($iconPath)):
  $svg = file_get_contents($iconPath);
  $svg = preg_replace('/width="[^"]*"/', '', $svg);
  $svg = preg_replace('/height="[^"]*"/', '', $svg);
  $svg = preg_replace('/fill="[^"]*"/', 'fill="currentColor"', $svg);
  $svg = preg_replace('/<svg/', '<svg class="' . ($class ?? '') . '"', $svg);
  echo $svg;
endif;
