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

      if ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes === 1 ? '1 minute ago' : $minutes . ' minutes ago';
      }

      if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours === 1 ? '1 hour ago' : $hours . ' hours ago';
      }

      if ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days === 1 ? '1 day ago' : $days . ' days ago';
      }

      if ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks === 1 ? '1 week ago' : $weeks . ' weeks ago';
      }

      if ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months === 1 ? '1 month ago' : $months . ' months ago';
      }

      $years = floor($diff / 31536000);
      return $years === 1 ? '1 year ago' : $years . ' years ago';
    },

    'formattedDate' => function() {
      return $this->date()->toDate('d M Y');
    }
  ]
]);
