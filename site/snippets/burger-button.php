<?php
/**
 * Animated burger button component
 * - Three bars slide off-screen when open
 * - X slides in from the side
 * - Toggles mobile menu open/closed
 */
?>
<div class="relative z-50 h-6 w-6 md:hidden" data-mobile-menu-trigger>
  <button type="button" class="relative h-full w-full cursor-pointer" aria-label="Toggle menu">
    <!-- Container for bars and X -->
    <div class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 overflow-hidden">
      <!-- Hamburger bars -->
      <div class="burger-bar burger-bar-1 h-0.5 w-5 bg-muted"></div>
      <div class="burger-bar burger-bar-2 h-0.5 w-5 bg-muted"></div>
      <div class="burger-bar burger-bar-3 h-0.5 w-5 bg-muted"></div>

      <!-- X bars (hidden initially, slide in) -->
      <div class="burger-x absolute inset-0 flex items-center justify-center -translate-x-10">
        <div class="burger-x-bar absolute h-0.5 w-5 origin-center rotate-45 bg-muted"></div>
        <div class="burger-x-bar absolute h-0.5 w-5 origin-center -rotate-45 bg-muted"></div>
      </div>
    </div>
  </button>
</div>
