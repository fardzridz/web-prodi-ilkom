document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-toggle-target]');

    if (! toggle) {
        return;
    }

    const target = document.getElementById(toggle.dataset.toggleTarget);

    if (target) {
        target.classList.toggle('is-open');
    }
});
