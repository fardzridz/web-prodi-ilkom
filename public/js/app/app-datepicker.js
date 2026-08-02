document.addEventListener('DOMContentLoaded', () => {
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
            AdminUI.closeDatePicker(picker, true);
        };

        const focusDate = (date) => {
            viewDate = new Date(date.getFullYear(), date.getMonth(), 1, 12);
            renderCalendar();
            dayGrid.querySelector(`[data-admin-date-day="${formatIsoDate(date)}"]`)?.focus();
        };

        const openDatePicker = (focusDay = false) => {
            AdminUI.closeAllCustomSelects();
            AdminUI.closeAllDatePickers(picker);
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
                AdminUI.closeDatePicker(picker);
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
            AdminUI.closeDatePicker(picker, true);
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

        AdminUI.datePickers.add(picker);
        syncDatePicker();
    };

    document.querySelectorAll('[data-admin-date-picker]').forEach(bindDatePicker);

    const activityStatus = document.querySelector('[data-activity-status]');
    const publishedField = document.querySelector('[data-published-field]');
    const publishedInput = document.querySelector('[data-published-input]');

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
});
