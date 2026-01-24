/**
 * Site interactivity
 */

(function () {
  "use strict";

  // Mobile menu
  const mobileMenu = document.querySelector("[data-mobile-menu]");
  const menuTriggers = document.querySelectorAll("[data-mobile-menu-trigger]");
  const menuClose = document.querySelector("[data-mobile-menu-close]");
  const menuBackdrop = document.querySelector("[data-mobile-menu-backdrop]");

  if (mobileMenu) {
    function openMenu() {
      mobileMenu.classList.remove("hidden");
      document.body.style.overflow = "hidden";
    }

    function closeMenu() {
      mobileMenu.classList.add("hidden");
      document.body.style.overflow = "";
    }

    menuTriggers.forEach((trigger) => {
      trigger.addEventListener("click", openMenu);
    });

    if (menuClose) {
      menuClose.addEventListener("click", closeMenu);
    }

    if (menuBackdrop) {
      menuBackdrop.addEventListener("click", closeMenu);
    }

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && !mobileMenu.classList.contains("hidden")) {
        closeMenu();
      }
    });
  }

  // Follow modal
  const followModal = document.querySelector("[data-follow-modal]");
  const followTriggers = document.querySelectorAll("[data-follow-trigger]");
  const followClose = document.querySelector("[data-follow-modal-close]");

  if (followModal) {
    followTriggers.forEach((trigger) => {
      trigger.addEventListener("click", () => {
        followModal.showModal();
      });
    });

    if (followClose) {
      followClose.addEventListener("click", () => {
        followModal.close();
      });
    }

    followModal.addEventListener("click", (e) => {
      if (e.target === followModal) {
        followModal.close();
      }
    });

    // RSS copy-to-clipboard
    const rssCopyButtons = followModal.querySelectorAll("[data-rss-copy]");

    rssCopyButtons.forEach((button) => {
      button.addEventListener("click", async () => {
        const url = button.dataset.rssUrl;
        if (!url) return;

        try {
          await navigator.clipboard.writeText(url);
          showToast("RSS link copied to clipboard", followModal);
        } catch (err) {
          // Fail silently
        }
      });
    });
  }

  // Share button (Web Share API with fallback)
  const shareButtons = document.querySelectorAll("[data-share-button]");

  shareButtons.forEach((button) => {
    button.addEventListener("click", async () => {
      const title = button.dataset.shareTitle || document.title;
      const url = button.dataset.shareUrl || window.location.href;

      if (navigator.share) {
        try {
          await navigator.share({ title, url });
        } catch (err) {
          if (err.name !== "AbortError") {
            fallbackShare(url);
          }
        }
      } else {
        fallbackShare(url);
      }
    });
  });

  function fallbackShare(url) {
    navigator.clipboard
      .writeText(url)
      .then(() => {
        showToast("Link copied to clipboard");
      })
      .catch(() => {
        window.prompt("Copy this link:", url);
      });
  }

  function showToast(message, container = null) {
    const toast = document.createElement("div");

    const baseClasses =
      "rounded-lg bg-accent px-4 py-2 font-mono text-xs text-center text-white shadow-lg opacity-0 transition-opacity duration-300";

    if (container) {
      toast.className = `absolute bottom-4 left-1/2 -translate-x-1/2 ${baseClasses}`;
      container.appendChild(toast);
    } else {
      toast.className = `fixed bottom-4 left-1/2 -translate-x-1/2 ${baseClasses}`;
      document.body.appendChild(toast);
    }

    toast.textContent = message;

    // Fade in
    requestAnimationFrame(() => {
      toast.classList.add("opacity-100");
    });

    // Fade out and remove
    setTimeout(() => {
      toast.classList.remove("opacity-100");
      setTimeout(() => toast.remove(), 300);
    }, 2000);
  }

  // Copy link buttons in share popovers
  document.querySelectorAll("[data-copy-link]").forEach((button) => {
    button.addEventListener("click", async () => {
      const url = button.dataset.copyLink;
      if (!url) return;

      try {
        await navigator.clipboard.writeText(url);
        showToast("Link copied to clipboard");

        // Close the popover
        const popover = button.closest("[popover]");
        if (popover) {
          popover.hidePopover();
        }
      } catch (err) {
        // Fallback for older browsers
        window.prompt("Copy this link:", url);
      }
    });
  });

  // Back button with history support
  document.querySelectorAll("[data-back-button]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const fallbackUrl = btn.dataset.fallbackUrl;
      const alwaysHome = btn.hasAttribute("data-always-home");

      // Static pages always go home
      if (alwaysHome) {
        window.location.href = fallbackUrl;
        return;
      }

      // Dynamic pages use history if coming from internal page
      const referrer = document.referrer;
      const currentHost = window.location.host;

      if (referrer && new URL(referrer).host === currentHost) {
        history.back();
      } else {
        window.location.href = fallbackUrl;
      }
    });
  });
})();
