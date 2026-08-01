const menuToggle = document.querySelector(".menu-toggle");
const mobileNav = document.querySelector(".mobile-nav");
const mobileClose = document.querySelector(".mobile-close");
const languageSwitcher = document.querySelector(".language-switcher");
const languageToggle = document.querySelector(".language-toggle");
const floatingPageNav = document.querySelector(".floating-page-nav");
const backToTopButton = document.querySelector(".sticky-action-top");
let menuCloseTimer;
let floatingPageNavStart = 0;

const reducedMotionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");

function resetPageTransitionState() {
  measureFloatingPageNav();
  updateBackToTopButton();
}

window.addEventListener("pageshow", resetPageTransitionState);

function setDocumentScrollLock(locked) {
  document.documentElement.classList.toggle("menu-open", locked);
  document.body.classList.toggle("menu-open", locked);
}

function setMenu(open) {
  if (!menuToggle || !mobileNav) return;
  window.clearTimeout(menuCloseTimer);
  mobileNav.classList.toggle("is-open", open);
  if (open) {
    setDocumentScrollLock(true);
  } else {
    menuCloseTimer = window.setTimeout(() => {
      setDocumentScrollLock(false);
    }, 1080);
  }
  menuToggle.setAttribute("aria-expanded", String(open));
  menuToggle.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
}

menuToggle?.addEventListener("click", () => {
  setMenu(!mobileNav.classList.contains("is-open"));
});

mobileClose?.addEventListener("click", () => setMenu(false));

mobileNav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => setMenu(false));
});

function setLanguageMenu(open) {
  if (!languageSwitcher || !languageToggle) return;
  languageSwitcher.classList.toggle("is-open", open);
  languageToggle.setAttribute("aria-expanded", String(open));
}

languageToggle?.addEventListener("click", (event) => {
  event.stopPropagation();
  setLanguageMenu(!languageSwitcher.classList.contains("is-open"));
});

languageSwitcher?.querySelectorAll(".language-menu a").forEach((link) => {
  link.addEventListener("click", () => setLanguageMenu(false));
});

document.addEventListener("click", (event) => {
  if (!languageSwitcher?.contains(event.target)) {
    setLanguageMenu(false);
  }
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    setMenu(false);
    setLanguageMenu(false);
  }
});

function updateFloatingPageNav() {
  if (!floatingPageNav) return;
  floatingPageNav.classList.toggle("is-stuck", window.scrollY >= floatingPageNavStart);
}

function getPageScrollDistance() {
  return Math.max(
    document.documentElement.scrollHeight,
    document.body.scrollHeight
  ) - (window.innerHeight || document.documentElement.clientHeight);
}

function updateBackToTopButton() {
  if (!backToTopButton) return;
  const scrollDistance = getPageScrollDistance();
  const threshold = scrollDistance * 0.1;
  backToTopButton.classList.toggle("is-visible", scrollDistance > 0 && window.scrollY >= threshold && window.scrollY > 0);
}

function scrollToPageTop() {
  window.scrollTo({
    top: 0,
    behavior: reducedMotionQuery.matches ? "auto" : "smooth",
  });
}

function measureFloatingPageNav() {
  if (!floatingPageNav) return;
  floatingPageNav.classList.remove("is-stuck");
  floatingPageNavStart = floatingPageNav.getBoundingClientRect().top + window.scrollY;
  updateFloatingPageNav();
}

window.addEventListener("load", () => {
  measureFloatingPageNav();
  updateBackToTopButton();
});
window.addEventListener("resize", () => {
  measureFloatingPageNav();
  updateBackToTopButton();
});
window.addEventListener("scroll", updateFloatingPageNav, { passive: true });
window.addEventListener("scroll", updateBackToTopButton, { passive: true });
backToTopButton?.addEventListener("click", scrollToPageTop);

measureFloatingPageNav();
updateBackToTopButton();

document.querySelectorAll("[data-load-more-target]").forEach((button) => {
  const target = document.querySelector(button.dataset.loadMoreTarget);
  const initial = Number(button.dataset.loadMoreInitial || 6);
  const step = Number(button.dataset.loadMoreStep || 6);
  let visibleLimit = initial;

  if (!target) return;

  const items = Array.from(target.querySelectorAll(".load-more-item"));

  function syncLoadMore(reset = false) {
    if (reset) {
      visibleLimit = initial;
    }

    const activeItems = items.filter((item) => !item.classList.contains("is-filtered-out"));

    items.forEach((item) => {
      const activeIndex = activeItems.indexOf(item);
      item.classList.toggle("is-hidden", activeIndex === -1 || activeIndex >= visibleLimit);
    });

    button.hidden = activeItems.length <= visibleLimit;
  }

  button.addEventListener("click", () => {
    visibleLimit += step;
    syncLoadMore();
  });

  target.syncLoadMore = syncLoadMore;
  syncLoadMore();
});

document.querySelectorAll("[data-filter-target]").forEach((button) => {
  const target = document.querySelector(button.dataset.filterTarget);
  if (!target) return;

  button.addEventListener("click", () => {
    const filterValue = button.dataset.filterCategory || "*";
    const filterGroup = document.querySelectorAll(`[data-filter-target="${button.dataset.filterTarget}"]`);

    filterGroup.forEach((filterButton) => {
      const isActive = filterButton === button;
      filterButton.classList.toggle("is-active", isActive);
      filterButton.setAttribute("aria-pressed", String(isActive));
    });

    target.querySelectorAll("[data-activity-category], [data-document-category]").forEach((item) => {
      const matchKey = item.dataset.activityCategory ?? item.dataset.documentCategory;
      item.classList.toggle("is-filtered-out", filterValue !== "*" && matchKey !== filterValue);
    });

    target.syncLoadMore?.(true);
  });
});

(function renderQuillDeltas() {
  function run() {
    const nodes = document.querySelectorAll("[data-quill-delta]");
    nodes.forEach((node) => {
      if (node.dataset.quillRendered === "1") return;
      if (typeof window.Quill === "undefined") {
        return;
      }
      const raw = node.getAttribute("data-quill-delta");
      if (!raw) return;
      let parsedString;
      try {
        parsedString = window.atob(raw);
      } catch (error) {
        node.dataset.quillRendered = "1";
        return;
      }
      let parsed;
      try {
        parsed = JSON.parse(parsedString);
      } catch (error) {
        node.dataset.quillRendered = "1";
        return;
      }

      const host = document.createElement("div");
      host.style.cssText = "position:absolute;left:-99999px;top:0;width:1px;height:1px;overflow:hidden;visibility:hidden;pointer-events:none;";
      document.body.appendChild(host);
      const probe = document.createElement("div");
      host.appendChild(probe);

      let html = "";
      try {
        const quill = new window.Quill(probe, {
          readOnly: true,
          modules: { toolbar: false }
        });
        quill.setContents(parsed);
        html = quill.getSemanticHTML();
        html = html.replace(/&nbsp;/g, " ");
      } catch (error) {
        node.dataset.quillRendered = "1";
        host.remove();
        return;
      }

      host.remove();

      if (html) {
        node.innerHTML = html;
      }
      node.dataset.quillRendered = "1";
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", run);
  } else {
    run();
  }

  if (typeof window.Quill === "undefined") {
    let attempts = 0;
    const interval = window.setInterval(() => {
      attempts += 1;
      if (typeof window.Quill !== "undefined" || attempts > 40) {
        window.clearInterval(interval);
        if (typeof window.Quill !== "undefined") run();
      }
    }, 100);
  }
})();

