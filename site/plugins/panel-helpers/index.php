<?php

Kirby::plugin('custom/panel-helpers', [
  'siteMethods' => [
    // Generate lowercase random ID for microposts
    // Static var ensures title & slug get same value in one request
    'newId' => function() {
      static $id = null;
      if ($id === null) {
        $id = strtolower(Str::random(16));
      }
      return $id;
    }
  ],
  'pageMethods' => [
    'timeAgo' => function() {
      $timestamp = $this->date()->toDate();
      $diff = time() - $timestamp;

      if ($diff < 60) return 'just now';
      if ($diff < 3600) return floor($diff / 60) . 'm ago';
      if ($diff < 86400) return floor($diff / 3600) . 'h ago';
      if ($diff < 604800) return floor($diff / 86400) . 'd ago';

      return $this->date()->toDate('M j');
    }
  ]
]);
