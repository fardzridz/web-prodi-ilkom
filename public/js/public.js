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

(function initDocumentPreviewModal() {
  const modal = document.querySelector("#document-preview-modal");
  const titleElement = document.querySelector("#document-preview-title");
  const bodyElement = document.querySelector("#document-preview-body");
  const backdrop = document.querySelector("[data-preview-backdrop]");

  if (!modal || !titleElement || !bodyElement) {
    return;
  }

  // Pustaka di-self-host di public/js/vendor (tanpa NPM) agar tidak bergantung CDN eksternal.
  const jszipSrc = "/js/vendor/jszip.min.js";
  const docxPreviewSrc = "/js/vendor/docx-preview.min.js";
  let docxPreviewPromise;

  function loadScript(src) {
    return new Promise((resolve, reject) => {
      const script = document.createElement("script");
      script.src = src;
      script.async = true;
      script.onload = () => resolve();
      script.onerror = () => reject(new Error("Script gagal dimuat: " + src));
      document.head.appendChild(script);
    });
  }

  function waitForGlobal(name) {
    return new Promise((resolve, reject) => {
      if (typeof window[name] !== "undefined") {
        resolve();
        return;
      }
      const startedAt = Date.now();
      const interval = window.setInterval(() => {
        if (typeof window[name] !== "undefined") {
          window.clearInterval(interval);
          resolve();
        } else if (Date.now() - startedAt > 8000) {
          window.clearInterval(interval);
          reject(new Error("Pustaka " + name + " tidak tersedia"));
        }
      }, 100);
    });
  }

  function loadDocxPreview() {
    if (typeof window.docx !== "undefined") {
      return Promise.resolve(window.docx);
    }
    if (!docxPreviewPromise) {
      docxPreviewPromise = (window.JSZip
        ? Promise.resolve()
        : loadScript(jszipSrc).then(() => waitForGlobal("JSZip")))
        .then(() => loadScript(docxPreviewSrc))
        .then(() => waitForGlobal("docx"))
        .then(() => window.docx)
        .catch((error) => {
          docxPreviewPromise = null;
          throw error;
        });
    }
    return docxPreviewPromise;
  }

  function showLoadingState() {
    const loading = document.createElement("p");
    loading.style.cssText = "margin:0;padding:32px 16px;text-align:center;color:#5b5c5e;";
    loading.textContent = "Memuat pratinjau…";
    bodyElement.innerHTML = "";
    bodyElement.appendChild(loading);
  }

  function showFallback(downloadUrl) {
    const panel = document.createElement("div");
    panel.style.cssText = "padding:32px 16px;text-align:center;color:#5b5c5e;";
    const message = document.createElement("p");
    message.style.cssText = "margin:0 0 18px;";
    message.textContent = "Format ini tidak mendukung pratinjau langsung.";
    panel.appendChild(message);
    if (downloadUrl) {
      const link = document.createElement("a");
      link.href = downloadUrl;
      link.textContent = "Unduh Dokumen";
      link.style.cssText = "display:inline-flex;align-items:center;min-height:38px;padding:0 14px;color:#ffffff;background:#29557b;font-size:12px;font-weight:700;text-decoration:none;";
      panel.appendChild(link);
    }
    bodyElement.innerHTML = "";
    bodyElement.appendChild(panel);
  }

  function renderPdf(url) {
    const iframe = document.createElement("iframe");
    iframe.src = url;
    iframe.title = "Pratinjau dokumen PDF";
    iframe.style.cssText = "display:block;width:100%;height:100%;border:0;";
    bodyElement.innerHTML = "";
    bodyElement.appendChild(iframe);
  }

  // docx-preview merender halaman sesuai ukuran asli (mis. A4 ≈ 794px) sehingga
  // di layar sempit isinya meluber dan terlihat "ke-zoom". Fungsi ini mengecilkan
  // hasil render agar selebar kontainer (fit to width) dan tetap di tengah —
  // di desktop tidak berubah.
  function fitDocxToContainer(container) {
    const wrapper = container.querySelector(".docx-wrapper") || container.firstElementChild;
    const page = wrapper ? wrapper.querySelector("section") || wrapper.firstElementChild : null;
    if (!wrapper || !page) {
      return;
    }
    // Reset penskalaan sebelumnya agar pengukuran memakai ukuran asli lagi.
    wrapper.style.transform = "";
    wrapper.style.transformOrigin = "";
    wrapper.style.height = "";
    const pageWidth = page.offsetWidth;
    const availableWidth = container.clientWidth;
    if (!pageWidth || !availableWidth) {
      return;
    }
    const scale = Math.min(1, availableWidth / pageWidth);
    if (scale >= 1) {
      return; // Konten sudah muat — tidak perlu diskalakan.
    }
    // Posisi kiri halaman relatif terhadap wrapper; bisa negatif karena wrapper
    // meratakan tengah (align-items: center) sehingga halaman yang lebih lebar
    // meluber ke kiri. Translate dipakai untuk mengembalikan halaman ke tengah
    // setelah diskalakan dari pojok kiri-atas.
    const pageLeft = page.getBoundingClientRect().left - wrapper.getBoundingClientRect().left;
    const translateX = (availableWidth - pageWidth * scale) / 2 - pageLeft * scale;
    const naturalHeight = wrapper.scrollHeight;
    wrapper.style.transformOrigin = "top left";
    wrapper.style.transform = `translate(${translateX}px, 0) scale(${scale})`;
    wrapper.style.height = `${naturalHeight * scale}px`;
  }

  function renderDocx(url, downloadUrl) {
    loadDocxPreview()
      .then((docx) => fetch(url)
        .then((response) => {
          if (!response.ok) {
            throw new Error("HTTP " + response.status);
          }
          return response.arrayBuffer();
        })
        .then((buffer) => {
          bodyElement.innerHTML = "";
          return docx.renderAsync(buffer, bodyElement);
        })
        .then(() => {
          fitDocxToContainer(bodyElement);
        }))
      .catch(() => {
        showFallback(downloadUrl);
      });
  }

  function renderPreview(trigger) {
    const url = trigger.dataset.previewUrl;
    const downloadUrl = trigger.dataset.previewDownload || null;
    const format = String(trigger.dataset.previewFormat || "").toLowerCase();

    titleElement.textContent = trigger.dataset.previewTitle || "";
    showLoadingState();

    if (url && format === "pdf") {
      renderPdf(url);
    } else if (url && format === "docx") {
      renderDocx(url, downloadUrl);
    } else {
      showFallback(downloadUrl);
    }
  }

  function setModalOpen(open) {
    modal.classList.toggle("hidden", !open);
    modal.classList.toggle("is-open", open);
    modal.hidden = !open;
    document.body.classList.toggle("overflow-hidden", open);
  }

  function openPreview(trigger) {
    renderPreview(trigger);
    setModalOpen(true);
  }

  function closePreview() {
    setModalOpen(false);
  }

  // Jaga penskalaan tetap pas saat ukuran/orientasi layar berubah (mis. rotasi HP).
  window.addEventListener("resize", () => {
    if (!modal.hidden && bodyElement.querySelector(".docx-wrapper")) {
      fitDocxToContainer(bodyElement);
    }
  });

  document.addEventListener("click", (event) => {
    const trigger = event.target.closest("[data-preview-url]");
    if (trigger) {
      event.preventDefault();
      openPreview(trigger);
      return;
    }
    if (modal.hidden || !backdrop) {
      return;
    }
    const isBackdropClick = backdrop.contains(event.target) && !event.target.closest("[data-preview-panel]");
    if (isBackdropClick || event.target.closest("[data-preview-close]")) {
      closePreview();
    }
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && !modal.hidden) {
      closePreview();
    }
  });
})();

