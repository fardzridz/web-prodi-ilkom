document.addEventListener('DOMContentLoaded', () => {
    const openCustomSelect = (customSelect, focusTarget = 'selected') => {
        const toggle = customSelect.querySelector('[data-admin-select-toggle]');
        const menu = customSelect.querySelector('[data-admin-select-menu]');

        if (! toggle || ! menu || toggle.disabled) {
            return;
        }

        AdminUI.closeAllCustomSelects(customSelect);
        customSelect.classList.add('is-open');
        menu.hidden = false;
        toggle.setAttribute('aria-expanded', 'true');

        if (! focusTarget) {
            return;
        }

        const options = [...menu.querySelectorAll('[data-admin-select-option]:not(:disabled)')];
        const target = focusTarget === 'last'
            ? options.at(-1)
            : menu.querySelector('[data-admin-select-option][aria-selected="true"]') ?? options[0];
        target?.focus();
    };

    const bindCustomSelect = (select, index) => {
        if (select.dataset.customSelectBound === 'true') {
            return;
        }

        select.dataset.customSelectBound = 'true';
        select.classList.add('admin-native-select');
        select.setAttribute('aria-hidden', 'true');
        select.tabIndex = -1;

        const customSelect = document.createElement('div');
        const toggle = document.createElement('button');
        const label = document.createElement('span');
        const chevron = document.createElement('i');
        const menu = document.createElement('div');
        const valueInput = document.createElement('input');
        const nativeName = select.getAttribute('name');
        const controlId = select.id || `admin-native-select-${index}`;
        const toggleId = `${controlId}-custom-toggle`;
        const menuId = `${controlId}-custom-menu`;
        const fieldLabel = select.closest('.activity-field')?.querySelector('label')?.textContent
            ?? select.closest('.activity-filter-select')?.querySelector('label')?.textContent
            ?? select.closest('label')?.querySelector('.sr-only')?.textContent
            ?? select.getAttribute('aria-label')
            ?? 'Pilih opsi';

        customSelect.className = 'admin-select';
        customSelect.dataset.adminSelect = '';
        customSelect.classList.toggle('is-invalid', select.getAttribute('aria-invalid') === 'true');

        toggle.id = toggleId;
        toggle.className = 'admin-select-toggle';
        toggle.type = 'button';
        toggle.dataset.adminSelectToggle = '';
        toggle.setAttribute('aria-label', fieldLabel.replace(/\s+/g, ' ').trim());
        toggle.setAttribute('aria-haspopup', 'listbox');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-controls', menuId);
        toggle.disabled = select.disabled;

        label.dataset.adminSelectLabel = '';
        chevron.className = 'fa-solid fa-chevron-down';
        chevron.setAttribute('aria-hidden', 'true');
        toggle.append(label, chevron);

        menu.id = menuId;
        menu.className = 'admin-select-menu';
        menu.dataset.adminSelectMenu = '';
        menu.setAttribute('role', 'listbox');
        menu.setAttribute('aria-labelledby', toggleId);
        menu.hidden = true;

        if (nativeName) {
            select.dataset.originalName = nativeName;
            select.removeAttribute('name');
            valueInput.type = 'hidden';
            valueInput.name = nativeName;
            valueInput.dataset.adminSelectValue = '';
        }

        [...select.options].forEach((nativeOption) => {
            const option = document.createElement('button');
            option.className = 'admin-select-option';
            option.type = 'button';
            option.dataset.adminSelectOption = '';
            option.dataset.value = nativeOption.value;
            option.setAttribute('role', 'option');
            option.textContent = nativeOption.textContent;
            option.disabled = nativeOption.disabled;
            menu.append(option);

            const commitOption = (closeMenu = true) => {
                [...select.options].forEach((item) => {
                    item.selected = item === nativeOption;
                });
                select.value = nativeOption.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));

                if (closeMenu) {
                    AdminUI.closeCustomSelect(customSelect, true);
                }
            };

            option.addEventListener('pointerdown', (event) => {
                event.preventDefault();
                event.stopPropagation();
                commitOption(false);
            });

            option.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                if (! nativeOption.selected) {
                    commitOption();
                } else {
                    AdminUI.closeCustomSelect(customSelect, true);
                }
            });
        });

        const syncCustomSelect = () => {
            const selectedOption = select.options[select.selectedIndex] ?? select.options[0];

            label.textContent = selectedOption?.textContent ?? 'Pilih opsi';
            customSelect.dataset.value = select.value;
            valueInput.value = select.value;
            toggle.disabled = select.disabled;
            customSelect.classList.toggle('is-invalid', select.getAttribute('aria-invalid') === 'true');
            menu.querySelectorAll('[data-admin-select-option]').forEach((option) => {
                const isSelected = option.dataset.value === select.value;
                option.classList.toggle('is-selected', isSelected);
                option.setAttribute('aria-selected', String(isSelected));
            });
        };

        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            AdminUI.closeAllDatePickers();

            if (customSelect.classList.contains('is-open')) {
                closeCustomSelect(customSelect);
            } else {
                openCustomSelect(customSelect, null);
            }
        });

        toggle.addEventListener('keydown', (event) => {
            if (! ['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) {
                return;
            }

            event.preventDefault();
            openCustomSelect(customSelect, event.key === 'ArrowUp' ? 'last' : 'selected');
        });

        menu.addEventListener('keydown', (event) => {
            const options = [...menu.querySelectorAll('[data-admin-select-option]:not(:disabled)')];
            const activeIndex = options.indexOf(document.activeElement);

            if (event.key === 'Escape') {
                event.preventDefault();
                closeCustomSelect(customSelect, true);

                return;
            }

            if (! ['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
                return;
            }

            event.preventDefault();
            const nextIndex = event.key === 'Home'
                ? 0
                : event.key === 'End'
                    ? options.length - 1
                    : event.key === 'ArrowDown'
                        ? Math.min(activeIndex + 1, options.length - 1)
                        : Math.max(activeIndex - 1, 0);
            options[nextIndex]?.focus();
        });

        select.addEventListener('change', syncCustomSelect);
        select.form?.addEventListener('reset', () => queueMicrotask(syncCustomSelect));
        select.insertAdjacentElement('afterend', customSelect);
        customSelect.append(toggle, menu);

        if (nativeName) {
            customSelect.insertAdjacentElement('afterend', valueInput);
        }

        const explicitLabel = select.id ? document.querySelector(`label[for="${select.id}"]`) : null;

        if (explicitLabel) {
            explicitLabel.htmlFor = toggleId;
        }

        AdminUI.customSelects.add(customSelect);
        syncCustomSelect();
    };

    document.querySelectorAll('.activity-filter-form select, .activity-field select')
        .forEach(bindCustomSelect);
});
