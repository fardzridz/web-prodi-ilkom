document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const sidebar = document.getElementById('admin-sidebar');
    const sidebarOverlay = document.getElementById('admin-sidebar-overlay');
    const sidebarOpenButton = document.querySelector('[data-sidebar-open]');
    const sidebarCloseButtons = document.querySelectorAll('[data-sidebar-close]');
    const profileToggle = document.querySelector('[data-profile-toggle]');
    const profileMenu = document.querySelector('[data-profile-menu]');
    const activityStatus = document.querySelector('[data-activity-status]');
    const publishedField = document.querySelector('[data-published-field]');
    const publishedInput = document.querySelector('[data-published-input]');
    const deleteDialog = document.querySelector('[data-delete-dialog]');
    const deleteNameOutput = document.querySelector('[data-delete-name-output]');
    const deleteConfirmButton = document.querySelector('[data-delete-confirm]');
    const customSelects = new Set();
    const datePickers = new Set();
    let pendingDeleteForm = null;

    const closeSidebar = () => {
        if (! sidebar || ! sidebarOverlay) {
            return;
        }

        sidebar.classList.remove('is-open');
        sidebarOverlay.classList.remove('is-visible');
        body.classList.remove('admin-menu-open');
        sidebarOpenButton?.setAttribute('aria-expanded', 'false');
    };

    const openSidebar = () => {
        if (! sidebar || ! sidebarOverlay) {
            return;
        }

        sidebar.classList.add('is-open');
        sidebarOverlay.classList.add('is-visible');
        body.classList.add('admin-menu-open');
        sidebarOpenButton?.setAttribute('aria-expanded', 'true');
    };

    sidebarOpenButton?.addEventListener('click', openSidebar);
    sidebarCloseButtons.forEach((button) => button.addEventListener('click', closeSidebar));
    sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeSidebar));

    const closeProfileMenu = () => {
        if (! profileToggle || ! profileMenu) {
            return;
        }

        profileMenu.hidden = true;
        profileToggle.setAttribute('aria-expanded', 'false');
    };

    profileToggle?.addEventListener('click', (event) => {
        event.stopPropagation();

        if (! profileMenu) {
            return;
        }

        const willOpen = profileMenu.hidden;
        profileMenu.hidden = ! willOpen;
        profileToggle.setAttribute('aria-expanded', String(willOpen));
    });

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

    const closeAllCustomSelects = (except = null) => {
        customSelects.forEach((customSelect) => {
            if (customSelect !== except) {
                closeCustomSelect(customSelect);
            }
        });
    };

    const openCustomSelect = (customSelect, focusTarget = 'selected') => {
        const toggle = customSelect.querySelector('[data-admin-select-toggle]');
        const menu = customSelect.querySelector('[data-admin-select-menu]');

        if (! toggle || ! menu || toggle.disabled) {
            return;
        }

        closeAllCustomSelects(customSelect);
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
                    closeCustomSelect(customSelect, true);
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
                    closeCustomSelect(customSelect, true);
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
            closeAllDatePickers();

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

        customSelects.add(customSelect);
        syncCustomSelect();
    };

    document.querySelectorAll('.activity-filter-form select, .activity-field select')
        .forEach(bindCustomSelect);

    const parseIsoDate = (value) => {
        const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value ?? '');

        if (! match) {
            return null;
        }

        const date = new Date(Number(match[1]), Number(match[2]) - 1, Number(match[3]), 12);

        return date.getFullYear() === Number(match[1])
            && date.getMonth() === Number(match[2]) - 1
            && date.getDate() === Number(match[3])
            ? date
            : null;
    };

    const formatIsoDate = (date) => [
        date.getFullYear(),
        String(date.getMonth() + 1).padStart(2, '0'),
        String(date.getDate()).padStart(2, '0'),
    ].join('-');

    const isSameDate = (first, second) => Boolean(first && second)
        && first.getFullYear() === second.getFullYear()
        && first.getMonth() === second.getMonth()
        && first.getDate() === second.getDate();

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

    const closeAllDatePickers = (except = null) => {
        datePickers.forEach((picker) => {
            if (picker !== except) {
                closeDatePicker(picker);
            }
        });
    };

    const bindDatePicker = (input, index) => {
        if (input.dataset.adminDatePickerBound === 'true') {
            return;
        }

        input.dataset.adminDatePickerBound = 'true';
        input.dataset.adminDateRequired = String(input.required);
        input.required = false;
        input.classList.add('admin-native-date');
        input.setAttribute('aria-hidden', 'true');
        input.tabIndex = -1;

        const picker = document.createElement('div');
        const toggle = document.createElement('button');
        const label = document.createElement('span');
        const calendarIcon = document.createElement('i');
        const menu = document.createElement('div');
        const calendarHeader = document.createElement('div');
        const previousButton = document.createElement('button');
        const monthOutput = document.createElement('strong');
        const nextButton = document.createElement('button');
        const weekdayGrid = document.createElement('div');
        const dayGrid = document.createElement('div');
        const calendarFooter = document.createElement('div');
        const todayButton = document.createElement('button');
        const clearButton = document.createElement('button');
        const timePanel = document.createElement('div');
        const timeLabel = document.createElement('label');
        const timeInput = document.createElement('input');
        const confirmDateTimeButton = document.createElement('button');
        const controlId = input.id || `admin-native-date-${index}`;
        const toggleId = `${controlId}-custom-toggle`;
        const menuId = `${controlId}-custom-menu`;
        const fieldLabel = input.closest('.activity-filter-date')?.querySelector('label')?.textContent
            ?? input.closest('.activity-field')?.querySelector('label')?.textContent
            ?? input.getAttribute('aria-label')
            ?? 'Pilih tanggal';
        const includesTime = input.type === 'datetime-local';
        const minDate = parseIsoDate(input.min?.slice(0, 10));
        const maxDate = parseIsoDate(input.max?.slice(0, 10));
        const today = new Date();
        let selectedDate = parseIsoDate(input.value?.slice(0, 10));
        let pendingDate = selectedDate;
        let viewDate = new Date(
            (selectedDate ?? today).getFullYear(),
            (selectedDate ?? today).getMonth(),
            1,
            12,
        );

        picker.className = 'admin-date-picker';
        picker.dataset.adminDatePickerUi = '';

        toggle.id = toggleId;
        toggle.className = 'admin-date-toggle';
        toggle.type = 'button';
        toggle.dataset.adminDateToggle = '';
        toggle.setAttribute('aria-label', fieldLabel.trim());
        toggle.setAttribute('aria-haspopup', 'dialog');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-controls', menuId);
        toggle.setAttribute('aria-required', input.dataset.adminDateRequired);

        label.dataset.adminDateLabel = '';
        calendarIcon.className = 'fa-regular fa-calendar';
        calendarIcon.setAttribute('aria-hidden', 'true');
        toggle.append(label, calendarIcon);

        menu.id = menuId;
        menu.className = 'admin-date-menu';
        menu.dataset.adminDateMenu = '';
        menu.setAttribute('role', 'dialog');
        menu.setAttribute('aria-modal', 'false');
        menu.setAttribute('aria-labelledby', toggleId);
        menu.hidden = true;

        calendarHeader.className = 'admin-date-header';
        previousButton.type = 'button';
        previousButton.setAttribute('aria-label', 'Bulan sebelumnya');
        previousButton.innerHTML = '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i>';
        monthOutput.setAttribute('aria-live', 'polite');
        nextButton.type = 'button';
        nextButton.setAttribute('aria-label', 'Bulan berikutnya');
        nextButton.innerHTML = '<i class="fa-solid fa-chevron-right" aria-hidden="true"></i>';
        calendarHeader.append(previousButton, monthOutput, nextButton);

        weekdayGrid.className = 'admin-date-weekdays';
        ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'].forEach((weekday) => {
            const item = document.createElement('span');
            item.textContent = weekday;
            weekdayGrid.append(item);
        });

        dayGrid.className = 'admin-date-days';
        dayGrid.setAttribute('role', 'grid');

        calendarFooter.className = 'admin-date-footer';
        todayButton.type = 'button';
        todayButton.textContent = 'Hari Ini';
        clearButton.type = 'button';
        clearButton.textContent = input.closest('.activity-filter-date') ? 'Hapus Filter' : 'Kosongkan';
        calendarFooter.append(todayButton, clearButton);

        timePanel.className = 'admin-date-time';
        timeLabel.htmlFor = `${controlId}-custom-time`;
        timeLabel.textContent = 'Waktu';
        timeInput.id = `${controlId}-custom-time`;
        timeInput.type = 'time';
        timeInput.step = '60';
        confirmDateTimeButton.type = 'button';
        confirmDateTimeButton.textContent = 'Pilih Jadwal';
        timePanel.append(timeLabel, timeInput, confirmDateTimeButton);

        menu.append(calendarHeader, weekdayGrid, dayGrid);

        if (includesTime) {
            menu.append(timePanel);
        }

        menu.append(calendarFooter);

        const formatVisibleDate = (date) => new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        }).format(date);
        const formatAccessibleDate = (date) => new Intl.DateTimeFormat('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(date);
        const inputTime = () => /T(\d{2}:\d{2})/.exec(input.value)?.[1] ?? '';
        const dateIsOutsideRange = (date) => Boolean(
            (minDate && formatIsoDate(date) < formatIsoDate(minDate))
            || (maxDate && formatIsoDate(date) > formatIsoDate(maxDate)),
        );

        const renderCalendar = () => {
            const year = viewDate.getFullYear();
            const month = viewDate.getMonth();
            const daysInMonth = new Date(year, month + 1, 0, 12).getDate();
            const startOffset = (new Date(year, month, 1, 12).getDay() + 6) % 7;
            const totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7;

            monthOutput.textContent = new Intl.DateTimeFormat('id-ID', {
                month: 'long',
                year: 'numeric',
            }).format(viewDate);
            dayGrid.replaceChildren();

            for (let cell = 0; cell < totalCells; cell += 1) {
                const dayNumber = cell - startOffset + 1;

                if (dayNumber < 1 || dayNumber > daysInMonth) {
                    const emptyCell = document.createElement('span');
                    emptyCell.className = 'admin-date-day-empty';
                    emptyCell.setAttribute('aria-hidden', 'true');
                    dayGrid.append(emptyCell);

                    continue;
                }

                const date = new Date(year, month, dayNumber, 12);
                const dayButton = document.createElement('button');
                const calendarSelection = includesTime ? pendingDate : selectedDate;
                const isSelected = isSameDate(date, calendarSelection);
                const isToday = isSameDate(date, today);
                const isDisabled = dateIsOutsideRange(date);

                dayButton.type = 'button';
                dayButton.className = 'admin-date-day';
                dayButton.dataset.adminDateDay = formatIsoDate(date);
                dayButton.textContent = String(dayNumber);
                dayButton.setAttribute('aria-label', formatAccessibleDate(date));
                dayButton.setAttribute('aria-pressed', String(isSelected));
                dayButton.classList.toggle('is-selected', isSelected);
                dayButton.classList.toggle('is-today', isToday);
                dayButton.disabled = isDisabled;
                dayButton.tabIndex = ! isDisabled && (isSelected || (! calendarSelection && isToday) || (! calendarSelection && ! isToday && dayNumber === 1)) ? 0 : -1;
                dayGrid.append(dayButton);
            }

            clearButton.disabled = ! selectedDate;
            todayButton.disabled = dateIsOutsideRange(today);
            confirmDateTimeButton.disabled = ! pendingDate || ! timeInput.value;
        };

        const syncDatePicker = () => {
            selectedDate = parseIsoDate(input.value?.slice(0, 10));
            pendingDate = selectedDate;
            const selectedTime = inputTime();

            label.textContent = selectedDate
                ? `${formatVisibleDate(selectedDate)}${includesTime && selectedTime ? ` · ${selectedTime.replace(':', '.')}` : ''}`
                : includesTime ? 'Pilih jadwal' : 'Semua tanggal';
            timeInput.value = selectedTime || '07:00';
            toggle.classList.toggle('has-value', Boolean(selectedDate));
            picker.classList.toggle('is-invalid', input.getAttribute('aria-invalid') === 'true');

            if (input.getAttribute('aria-invalid') === 'true') {
                toggle.setAttribute('aria-invalid', 'true');
            } else {
                toggle.removeAttribute('aria-invalid');
            }

            renderCalendar();
        };

        const commitDate = (date, time = '') => {
            input.value = includesTime ? `${formatIsoDate(date)}T${time || '07:00'}` : formatIsoDate(date);
            input.removeAttribute('aria-invalid');
            input.dispatchEvent(new Event('change', { bubbles: true }));
            syncDatePicker();
            closeDatePicker(picker, true);
        };

        const focusDate = (date) => {
            viewDate = new Date(date.getFullYear(), date.getMonth(), 1, 12);
            renderCalendar();
            dayGrid.querySelector(`[data-admin-date-day="${formatIsoDate(date)}"]`)?.focus();
        };

        const openDatePicker = (focusDay = false) => {
            closeAllCustomSelects();
            closeAllDatePickers(picker);
            picker.classList.add('is-open');
            menu.hidden = false;
            toggle.setAttribute('aria-expanded', 'true');
            pendingDate = selectedDate;
            timeInput.value = inputTime() || '07:00';
            renderCalendar();

            if (focusDay) {
                const preferredDate = selectedDate
                    ?? (today.getFullYear() === viewDate.getFullYear() && today.getMonth() === viewDate.getMonth() ? today : viewDate);
                focusDate(preferredDate);
            }
        };

        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            if (picker.classList.contains('is-open')) {
                closeDatePicker(picker);
            } else {
                openDatePicker();
            }
        });

        toggle.addEventListener('keydown', (event) => {
            if (! ['ArrowDown', 'Enter', ' '].includes(event.key)) {
                return;
            }

            event.preventDefault();
            openDatePicker(true);
        });

        previousButton.addEventListener('click', (event) => {
            event.stopPropagation();
            viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() - 1, 1, 12);
            renderCalendar();
        });

        nextButton.addEventListener('click', (event) => {
            event.stopPropagation();
            viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth() + 1, 1, 12);
            renderCalendar();
        });

        dayGrid.addEventListener('click', (event) => {
            event.stopPropagation();
            const dayButton = event.target.closest('[data-admin-date-day]');
            const date = parseIsoDate(dayButton?.dataset.adminDateDay);

            if (date) {
                if (includesTime) {
                    pendingDate = date;
                    renderCalendar();
                    timeInput.focus();
                } else {
                    commitDate(date);
                }
            }
        });

        dayGrid.addEventListener('keydown', (event) => {
            const dayButton = event.target.closest('[data-admin-date-day]');
            const activeDate = parseIsoDate(dayButton?.dataset.adminDateDay);

            if (! activeDate) {
                return;
            }

            const dayMovement = {
                ArrowLeft: -1,
                ArrowRight: 1,
                ArrowUp: -7,
                ArrowDown: 7,
            }[event.key];

            if (dayMovement) {
                event.preventDefault();
                activeDate.setDate(activeDate.getDate() + dayMovement);
                focusDate(activeDate);

                return;
            }

            if (event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                const weekday = (activeDate.getDay() + 6) % 7;
                activeDate.setDate(activeDate.getDate() + (event.key === 'Home' ? -weekday : 6 - weekday));
                focusDate(activeDate);

                return;
            }

            if (event.key === 'PageUp' || event.key === 'PageDown') {
                event.preventDefault();
                const movement = event.key === 'PageUp' ? -1 : 1;
                const targetMonth = new Date(activeDate.getFullYear(), activeDate.getMonth() + movement, 1, 12);
                const targetDay = Math.min(activeDate.getDate(), new Date(targetMonth.getFullYear(), targetMonth.getMonth() + 1, 0, 12).getDate());
                focusDate(new Date(targetMonth.getFullYear(), targetMonth.getMonth(), targetDay, 12));
            }
        });

        todayButton.addEventListener('click', (event) => {
            event.stopPropagation();

            if (includesTime) {
                pendingDate = today;
                viewDate = new Date(today.getFullYear(), today.getMonth(), 1, 12);
                renderCalendar();
                timeInput.focus();
            } else {
                commitDate(today);
            }
        });

        timeInput.addEventListener('input', renderCalendar);

        confirmDateTimeButton.addEventListener('click', (event) => {
            event.stopPropagation();

            if (pendingDate && timeInput.value) {
                commitDate(pendingDate, timeInput.value);
            }
        });

        clearButton.addEventListener('click', (event) => {
            event.stopPropagation();
            input.value = '';
            input.removeAttribute('aria-invalid');
            input.dispatchEvent(new Event('change', { bubbles: true }));
            viewDate = new Date(today.getFullYear(), today.getMonth(), 1, 12);
            syncDatePicker();
            closeDatePicker(picker, true);
        });

        menu.addEventListener('click', (event) => event.stopPropagation());
        input.addEventListener('change', syncDatePicker);
        input.form?.addEventListener('reset', () => queueMicrotask(syncDatePicker));
        input.form?.addEventListener('submit', (event) => {
            const field = input.closest('.activity-field');
            const isRequired = input.dataset.adminDateRequired === 'true';

            if (isRequired && ! field?.hidden && ! input.value) {
                event.preventDefault();
                picker.classList.add('is-invalid');
                toggle.setAttribute('aria-invalid', 'true');
                toggle.focus();
            }
        });
        input.insertAdjacentElement('afterend', picker);
        picker.append(toggle, menu);

        const explicitLabel = document.querySelector(`label[for="${controlId}"]`);

        if (explicitLabel) {
            explicitLabel.htmlFor = toggleId;
        }

        datePickers.add(picker);
        syncDatePicker();
    };

    document.querySelectorAll('[data-admin-date-picker]').forEach(bindDatePicker);

    const syncPublicationField = () => {
        if (! activityStatus || ! publishedField || ! publishedInput) {
            return;
        }

        const isScheduled = activityStatus.value === 'scheduled';
        publishedField.hidden = ! isScheduled;

        if (publishedInput.dataset.adminDatePickerBound === 'true') {
            publishedInput.dataset.adminDateRequired = String(isScheduled);
            publishedInput.required = false;
            publishedInput.nextElementSibling?.querySelector('[data-admin-date-toggle]')
                ?.setAttribute('aria-required', String(isScheduled));
        } else {
            publishedInput.required = isScheduled;
        }

        if (! isScheduled) {
            publishedInput.value = '';
        }
    };

    activityStatus?.addEventListener('change', syncPublicationField);
    syncPublicationField();

    document.addEventListener('click', (event) => {
        closeAllCustomSelects();
        closeAllDatePickers();

        if (profileMenu && profileToggle && ! profileMenu.contains(event.target) && ! profileToggle.contains(event.target)) {
            closeProfileMenu();
        }

        const dismissButton = event.target.closest('[data-alert-dismiss]');
        dismissButton?.closest('[data-flash-alert]')?.remove();

        const deleteTrigger = event.target.closest('[data-delete-trigger]');

        if (! deleteTrigger) {
            return;
        }

        pendingDeleteForm = document.getElementById(deleteTrigger.dataset.deleteForm);

        if (! pendingDeleteForm) {
            return;
        }

        if (deleteNameOutput) {
            deleteNameOutput.textContent = deleteTrigger.dataset.deleteName ?? '';
        }

        if (deleteDialog?.showModal) {
            deleteDialog.showModal();

            return;
        }

        if (window.confirm(`Hapus kegiatan ${deleteTrigger.dataset.deleteName ?? 'ini'}?`)) {
            pendingDeleteForm.requestSubmit();
        }
    });

    document.addEventListener('focusin', (event) => {
        customSelects.forEach((customSelect) => {
            if (! customSelect.contains(event.target)) {
                closeCustomSelect(customSelect);
            }
        });

        datePickers.forEach((picker) => {
            if (! picker.contains(event.target)) {
                closeDatePicker(picker);
            }
        });
    });

    deleteConfirmButton?.addEventListener('click', () => pendingDeleteForm?.requestSubmit());
    deleteDialog?.addEventListener('close', () => {
        pendingDeleteForm = null;
    });

    const bindFileInput = (input) => {
        if (input.dataset.fileInputBound === 'true') {
            return;
        }

        input.dataset.fileInputBound = 'true';
        const output = input.closest('[data-file-field]')?.querySelector('[data-file-name-output]')
            ?? input.closest('form')?.querySelector('[data-file-name-output]');

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            const field = input.closest('[data-file-field]');
            const buttonLabel = field?.querySelector('.slide-file-button strong');
            const preview = field?.closest('[data-slide-item]')?.querySelector('.slide-editor-preview');

            if (output && file) {
                output.textContent = `${file.name} · ${(file.size / (1024 * 1024)).toFixed(2)} MB`;
            }

            if (buttonLabel && file) {
                buttonLabel.textContent = 'Ganti gambar';
            }

            if (preview && file && file.type.startsWith('image/')) {
                const objectUrl = URL.createObjectURL(file);
                const existingImage = preview.querySelector('img');

                if (existingImage) {
                    if (existingImage.dataset.objectUrl) {
                        URL.revokeObjectURL(existingImage.dataset.objectUrl);
                    }

                    existingImage.src = objectUrl;
                    existingImage.dataset.objectUrl = objectUrl;
                } else {
                    preview.classList.remove('is-empty');
                    preview.innerHTML = `<img src="${objectUrl}" alt="Pratinjau gambar terpilih" data-object-url="${objectUrl}"><span>Siap disimpan</span>`;
                    const image = preview.querySelector('img');

                    if (image) {
                        image.dataset.objectUrl = objectUrl;
                    }
                }
            }

            if (file && file.type.startsWith('image/') && field?.closest('[data-home-slide-list]')) {
                document.dispatchEvent(new CustomEvent('home-preview:refresh-slide'));
            }
        });
    };

    document.querySelectorAll('[data-file-input]').forEach(bindFileInput);

    document.querySelectorAll('[data-character-count]').forEach((input) => {
        const output = input.closest('.activity-field')?.querySelector('[data-character-count-output]')
            ?? input.closest('.profile-field-card')?.querySelector('[data-character-count-output]');

        const syncCharacterCount = () => {
            if (output) {
                output.textContent = new Intl.NumberFormat('id-ID').format(input.value.length);
            }
        };

        input.addEventListener('input', syncCharacterCount);
        syncCharacterCount();
    });

    const slideList = document.querySelector('[data-home-slide-list]');
    const slideTemplate = document.querySelector('[data-home-slide-template]');
    const slideAddButton = document.querySelector('[data-home-slide-add]');
    const homePreview = document.querySelector('[data-home-preview]');

    const escapeHtml = (value) => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const syncHomePreviewText = () => {
        if (! homePreview) {
            return;
        }

        const titleField = document.querySelector('[data-home-preview-field="title"]');
        const subtitleField = document.querySelector('[data-home-preview-field="subtitle"]');
        const ctaField = document.querySelector('[data-home-preview-field="cta"]');
        const welcomeTitleField = document.querySelector('[data-home-preview-field="welcome-title"]');
        const welcomeDescriptionField = document.querySelector('[data-home-preview-field="welcome-description"]');

        const titleTarget = homePreview.querySelector('[data-home-preview-title]');
        const subtitleTarget = homePreview.querySelector('[data-home-preview-subtitle]');
        const ctaTarget = homePreview.querySelector('[data-home-preview-cta]');
        const welcomeTitleTarget = homePreview.querySelector('[data-home-preview-welcome-title]');
        const welcomeDescriptionTarget = homePreview.querySelector('[data-home-preview-welcome-description]');

        if (titleTarget && titleField) {
            const title = titleField.value.trim() || 'Judul beranda';
            titleTarget.innerHTML = escapeHtml(title).replaceAll('\n', '<br>');
        }

        if (subtitleTarget && subtitleField) {
            subtitleTarget.textContent = subtitleField.value.trim() || 'Kalimat pembuka beranda';
        }

        if (ctaTarget && ctaField) {
            ctaTarget.textContent = ctaField.value.trim() || 'Teks tombol';
        }

        if (welcomeTitleTarget && welcomeTitleField) {
            welcomeTitleTarget.textContent = welcomeTitleField.value.trim() || 'Judul sambutan';
        }

        if (welcomeDescriptionTarget && welcomeDescriptionField) {
            welcomeDescriptionTarget.textContent = welcomeDescriptionField.value.trim() || 'Isi sambutan beranda';
        }
    };

    const syncHomePreviewSlide = () => {
        if (! homePreview) {
            return;
        }

        const visual = homePreview.querySelector('[data-home-preview-visual]');

        if (! visual) {
            return;
        }

        const activeSlide = slideList?.querySelector('[data-slide-item]');
        const slideImage = activeSlide?.querySelector('.slide-editor-preview img');

        if (slideImage?.src) {
            visual.innerHTML = `<img src="${slideImage.src}" alt="${escapeHtml(slideImage.alt || 'Pratinjau slide hero')}">`;

            return;
        }

        visual.innerHTML = '<i class="fa-solid fa-panorama" aria-hidden="true"></i>';
    };

    const queueSlideForDeletion = (existingPath) => {
        if (! slideList || ! existingPath) {
            return;
        }

        const index = Number(slideList.dataset.nextIndex ?? 0);
        const tombstone = document.createElement('div');
        tombstone.hidden = true;
        tombstone.setAttribute('data-slide-tombstone', 'true');
        tombstone.innerHTML = `
            <input type="hidden" name="slides[${index}][existing_path]" value="${escapeHtml(existingPath)}">
            <input type="hidden" name="slides[${index}][remove]" value="1">
            <input type="hidden" name="slides[${index}][alt]" value="">
        `;
        slideList.append(tombstone);
        slideList.dataset.nextIndex = String(index + 1);
    };

    document.querySelectorAll('[data-home-preview-field]').forEach((field) => {
        field.addEventListener('input', syncHomePreviewText);
    });

    document.addEventListener('home-preview:refresh-slide', syncHomePreviewSlide);

    syncHomePreviewText();
    syncHomePreviewSlide();

    slideAddButton?.addEventListener('click', () => {
        if (! slideList || ! slideTemplate || slideList.querySelectorAll('[data-slide-item]').length >= 5) {
            return;
        }

        const index = Number(slideList.dataset.nextIndex ?? 0);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = slideTemplate.innerHTML.replaceAll('__INDEX__', String(index)).trim();
        slideList.querySelector('[data-slide-empty]')?.remove();
        slideList.append(wrapper.firstElementChild);
        slideList.dataset.nextIndex = String(index + 1);
        slideList.querySelectorAll('[data-file-input]').forEach(bindFileInput);
        syncHomePreviewSlide();
    });

    slideList?.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-slide-remove]');

        if (! removeButton) {
            return;
        }

        const item = removeButton.closest('[data-slide-item]');

        if (! item) {
            return;
        }

        const existingPath = item.dataset.existingPath
            || item.querySelector('input[name*="[existing_path]"]')?.value
            || '';

        if (existingPath) {
            queueSlideForDeletion(existingPath);
        }

        item.remove();
        syncHomePreviewSlide();
    });

    const footerLinkList = document.querySelector('[data-footer-link-list]');
    const footerLinkTemplate = document.querySelector('[data-footer-link-template]');
    const footerLinkAddButton = document.querySelector('[data-footer-link-add]');

    footerLinkAddButton?.addEventListener('click', () => {
        if (! footerLinkList || ! footerLinkTemplate || footerLinkList.querySelectorAll('[data-footer-link-item]').length >= 10) {
            return;
        }

        const index = Number(footerLinkList.dataset.nextIndex ?? 0);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = footerLinkTemplate.innerHTML.replaceAll('__INDEX__', String(index)).trim();
        footerLinkList.querySelector('[data-footer-link-empty]')?.remove();
        footerLinkList.append(wrapper.firstElementChild);
        footerLinkList.dataset.nextIndex = String(index + 1);
    });

    footerLinkList?.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-footer-link-remove]');

        if (removeButton) {
            removeButton.closest('[data-footer-link-item]')?.remove();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        closeSidebar();
        closeProfileMenu();
        closeAllCustomSelects();
        closeAllDatePickers();
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });

    document.querySelectorAll('form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) {
                return;
            }

            const submitter = event.submitter;

            if (
                submitter
                && (
                    submitter.value === 'cancel'
                    || submitter.hasAttribute('data-delete-cancel')
                    || (
                        submitter.classList.contains('admin-button-secondary')
                        && ! submitter.classList.contains('admin-button-primary')
                    )
                )
            ) {
                return;
            }

            let button = submitter instanceof HTMLButtonElement ? submitter : null;

            if (
                ! button
                || (
                    ! button.classList.contains('admin-button-primary')
                    && ! button.classList.contains('content-save-button')
                )
            ) {
                button = form.querySelector('button[type="submit"].admin-button-primary, button[type="submit"].content-save-button');
            }

            if (
                ! (button instanceof HTMLButtonElement)
                || button.disabled
                || button.classList.contains('is-loading')
                || (
                    ! button.classList.contains('admin-button-primary')
                    && ! button.classList.contains('content-save-button')
                )
            ) {
                return;
            }

            button.classList.add('is-loading');
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            button.setAttribute('aria-label', 'Menyimpan...');

            const loader = document.createElement('span');
            loader.className = 'admin-save-loader';
            loader.setAttribute('aria-hidden', 'true');

            const label = document.createElement('span');
            label.className = 'admin-save-label';
            label.textContent = 'Menyimpan...';

            button.replaceChildren(loader, label);

            form.querySelectorAll('button[type="submit"]').forEach((otherButton) => {
                if (otherButton !== button) {
                    otherButton.disabled = true;
                }
            });
        });
    });
});

(function () {
    document.addEventListener("change", (event) => {
        const input = event.target;
        if (!input.matches("[data-image-input]")) return;
        if (!input.files?.length) return;
        const [file] = input.files;

        const upload = input.closest("[data-image-upload]");
        const preview = upload?.querySelector("[data-image-preview]");
        const fileName = input.closest(".activity-field")?.querySelector("[data-image-file-name]");
        const overlayLabel = upload?.querySelector("[data-image-overlay-label]");

        if (fileName) fileName.textContent = file.name;

        if (file.size > 2_097_152) {
            const existing = document.querySelector(".activity-field-error[id^='activity-image']");
            if (!existing) {
                const error = document.createElement("small");
                error.className = "activity-field-error";
                error.id = "activity-image-error";
                error.textContent = "Ukuran gambar maksimal 2 MB.";
                input.insertAdjacentElement("afterend", error);
            }
            input.value = "";
            return;
        }

        if (preview && file.type.startsWith("image/")) {
            const objectUrl = URL.createObjectURL(file);
            const existingImg = preview.querySelector("img");
            if (existingImg) {
                URL.revokeObjectURL(existingImg.src);
                existingImg.remove();
            }
            const img = document.createElement("img");
            img.src = objectUrl;
            img.alt = "Pratinjau gambar terpilih";
            img.dataset.objectUrl = objectUrl;
            preview.appendChild(img);
            preview.removeAttribute("data-empty");
            if (overlayLabel) overlayLabel.textContent = "Ganti gambar";
        }
    });
})();

(function initQuillEditors() {
    if (typeof window.Quill === "undefined") {
        return;
    }

    const FONT_WHITELIST = ["Fakt", "Grold", "GroldSlim", "GroldRounded", "Gotcha", "default"];

    const FontClass = window.Quill.import("formats/font");
    if (FontClass && Array.isArray(FontClass.whitelist)) {
        FontClass.whitelist = FONT_WHITELIST;
        window.Quill.register(FontClass, true);
    }

    const FORMATS = [
        "background", "bold", "color", "font", "code",
        "italic", "link", "size", "strike", "script", "underline",
        "blockquote", "header", "indent", "list",
        "align", "direction", "code-block",
        "formula", "image", "video"
    ];

    const TOOLBAR = [
        [{ font: FONT_WHITELIST }, { size: ["small", false, "large", "huge"] }],
        [{ header: [1, 2, 3, 4, 5, 6, false] }],
        ["bold", "italic", "underline", "strike"],
        [{ color: [] }, { background: [] }],
        [{ script: "sub" }, { script: "super" }],
        [{ list: "ordered" }, { list: "bullet" }, { list: "check" }],
        [{ indent: "-1" }, { indent: "+1" }],
        [{ align: [] }],
        ["blockquote", "code-block"],
        ["link", "image", "video"],
        ["clean"]
    ];

    const editors = document.querySelectorAll(".quill-editor");
    editors.forEach((element) => {
        const hidden = document.getElementById(element.id + "-hidden");
        const initialValue = hidden ? hidden.value : "";

        let initialHtml = "";
        if (initialValue) {
            const trimmed = initialValue.trim();
            if (trimmed.startsWith("{")) {
                const probeHost = document.createElement("div");
                probeHost.style.cssText = "position:absolute;left:-99999px;top:0;width:1px;height:1px;overflow:hidden;visibility:hidden;pointer-events:none;";
                document.body.appendChild(probeHost);
                const probeEditor = document.createElement("div");
                probeHost.appendChild(probeEditor);
                try {
                    const probeQuill = new window.Quill(probeEditor, {
                        readOnly: true,
                        modules: { toolbar: false }
                    });
                    probeQuill.setContents(JSON.parse(initialValue));
                    initialHtml = probeQuill.getSemanticHTML().replace(/&nbsp;/g, " ");
                } catch (error) {
                    console.error("Quill Delta conversion failed:", error.message);
                }
                probeHost.remove();
            } else {
                initialHtml = initialValue;
            }
        }
        if (hidden) hidden.value = initialHtml;

        const quill = new window.Quill(element, {
            theme: "snow",
            placeholder: "Tuliskan konten di sini...",
            formats: FORMATS,
            modules: { toolbar: TOOLBAR }
        });

        if (initialHtml) {
            try {
                quill.clipboard.dangerouslyPasteHTML(initialHtml);
            } catch (error) {
                console.error("Quill HTML paste failed:", error.message);
            }
        }

        const syncHidden = () => {
            if (hidden) {
                hidden.value = quill.getSemanticHTML().replace(/&nbsp;/g, " ");
            }
        };

        quill.on("text-change", syncHidden);
    });
})();