document.addEventListener('DOMContentLoaded', () => {
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

    AdminUI.bindFileInput = bindFileInput;
});
