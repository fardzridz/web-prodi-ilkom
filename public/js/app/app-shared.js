window.AdminUI = (() => {
    const customSelects = new Set();
    const datePickers = new Set();

    const closeCustomSelect = (customSelect, restoreFocus = false) => {
        const toggle = customSelect?.querySelector('[data-admin-select-toggle]');
        const menu = customSelect?.querySelector('[data-admin-select-menu]');

        if (! toggle || ! menu) {
            return;
        }

        customSelect.classList.remove('is-open');
        menu.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');

        if (restoreFocus) {
            toggle.focus();
        }
    };

    const closeDatePicker = (picker, restoreFocus = false) => {
        const toggle = picker?.querySelector('[data-admin-date-toggle]');
        const menu = picker?.querySelector('[data-admin-date-menu]');

        if (! toggle || ! menu) {
            return;
        }

        picker.classList.remove('is-open');
        menu.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');

        if (restoreFocus) {
            toggle.focus();
        }
    };

    const closeAllCustomSelects = (except = null) => {
        customSelects.forEach((customSelect) => {
            if (customSelect !== except) {
                closeCustomSelect(customSelect);
            }
        });
    };

    const closeAllDatePickers = (except = null) => {
        datePickers.forEach((picker) => {
            if (picker !== except) {
                closeDatePicker(picker);
            }
        });
    };

    return {
        customSelects,
        datePickers,
        closeCustomSelect,
        closeDatePicker,
        closeAllCustomSelects,
        closeAllDatePickers,
    };
})();
