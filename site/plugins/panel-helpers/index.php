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
  ]
]);
