(function () {
    const els = document.querySelectorAll('[data-reveal]');
    if (!('IntersectionObserver' in window) || !els.length) return;
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-revealed');
            io.unobserve(entry.target);
        });
    }, { threshold: 0.15 });
    els.forEach((el) => io.observe(el));
})();

function replayAnimations(root) {
    root.querySelectorAll('.anim-fade, .anim-fade-up, .anim-fade-down, .anim-fade-left, .anim-fade-right, .anim-zoom').forEach((el) => {
        el.style.animation = 'none';
        void el.offsetWidth;
        el.style.animation = '';
    });
}

(function () {
    const header = document.querySelector('header');
    if (!header || !window.matchMedia('(min-width: 1px)').matches) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    let lastY = window.scrollY;
    const HIDE_AFTER = 160;
    window.addEventListener('scroll', () => {
        const y = window.scrollY;
        const delta = y - lastY;
        lastY = y;
        if (y < HIDE_AFTER) { header.classList.remove('-translate-y-full'); return; }
        if (delta < -4) header.classList.remove('-translate-y-full');
        else if (delta > 4) header.classList.add('-translate-y-full');
    }, { passive: true });
})();

(function () {
    const btn = document.getElementById('scroll-top');
    if (!btn) return;
    const doc = document.documentElement;
    const toggle = () => {
        const hidden = window.scrollY < 600;
        btn.style.display = hidden ? 'none' : 'flex';
    };
    const update = () => {
        const max = doc.scrollHeight - window.innerHeight;
        const deg = max > 0 ? (window.scrollY / max) * 360 : 0;
        btn.style.setProperty('--progress', deg.toFixed(1) + 'deg');
    };
    const onScroll = () => { update(); toggle(); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();

document.addEventListener('mousemove', (e) => {
    const btn = e.target.closest('.btn');
    if (!btn) return;
    const r = btn.getBoundingClientRect();
    btn.style.setProperty('--mx', (e.clientX - r.left) + 'px');
    btn.style.setProperty('--my', (e.clientY - r.top) + 'px');
});

document.querySelectorAll('.dropdown').forEach((dd) => {
    const trigger = dd.querySelector('.dropdown-trigger');
    const menu = dd.querySelector('.dropdown-menu');
    const items = Array.from(dd.querySelectorAll('[role="menuitem"]'));

    function open() {
        dd.classList.add('is-open');
        menu.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
    }
    function close() {
        dd.classList.remove('is-open');
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
    }
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.hidden ? open() : close();
    });
    document.addEventListener('click', (e) => {
        if (!dd.contains(e.target)) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { close(); trigger.focus(); }
        if (!dd.classList.contains('is-open')) return;
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            const i = items.indexOf(document.activeElement);
            const j = (i + (e.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length;
            items[j].focus();
        }
    });
});

(function () {
    const btn = document.getElementById('menu-btn');
    const menu = document.getElementById('mobile-menu');
    if (!btn || !menu) return;
    const isClosed = () => menu.style.transform === 'translateX(100%)' || !menu.style.transform;
    const setOpen = (open) => {
        menu.style.transform = open ? 'translateX(0)' : 'translateX(100%)';
        document.body.classList.toggle('overflow-hidden', open);
        btn.setAttribute('aria-expanded', String(open));
        btn.querySelector('[data-icon-open]').classList.toggle('hidden', open);
        btn.querySelector('[data-icon-close]').classList.toggle('hidden', !open);
        if (open) replayAnimations(menu);
    };
    btn.addEventListener('click', () => setOpen(isClosed()));
    const closeBtn = document.getElementById('menu-close');
    if (closeBtn) closeBtn.addEventListener('click', () => setOpen(false));
    menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') setOpen(false);
    });
})();
