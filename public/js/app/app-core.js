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
    document.addEventListener('click', (event) => {
        AdminUI.closeAllCustomSelects();
        AdminUI.closeAllDatePickers();

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
        AdminUI.customSelects.forEach((customSelect) => {
            if (! customSelect.contains(event.target)) {
                AdminUI.closeCustomSelect(customSelect);
            }
        });

        AdminUI.datePickers.forEach((picker) => {
            if (! picker.contains(event.target)) {
                AdminUI.closeDatePicker(picker);
            }
        });
    });

    deleteConfirmButton?.addEventListener('click', () => pendingDeleteForm?.requestSubmit());
    deleteDialog?.addEventListener('close', () => {
        pendingDeleteForm = null;
    });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        closeSidebar();
        closeProfileMenu();
        AdminUI.closeAllCustomSelects();
        AdminUI.closeAllDatePickers();
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
});
