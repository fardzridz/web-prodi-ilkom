document.addEventListener('DOMContentLoaded', () => {
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
        slideList.querySelectorAll('[data-file-input]').forEach(AdminUI.bindFileInput);
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
});
