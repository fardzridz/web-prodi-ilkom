if (typeof window.Quill === 'undefined') {
    window.addEventListener('quill:loaded', initQuillEditors, { once: true });
} else {
    initQuillEditors();
}

function initQuillEditors() {
    document.querySelectorAll('.quill-editor').forEach(function (container) {
        const hiddenInput = document.getElementById(container.id + '-hidden');
        if (!hiddenInput) return;

        const quill = new Quill(container, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }, { indent: '-1' }, { indent: '+1' }],
                    [{ align: [] }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
                    ['clean'],
                ],
            },
            placeholder: 'Tulis konten di sini...',
        });

        const existingContent = hiddenInput.value.trim();
        if (existingContent) {
            quill.clipboard.dangerouslyPasteHTML(existingContent);
        }

        const normalizeWhitespace = function (html) {
            return html.replace(/&nbsp;/g, ' ').replace(/\u00A0/g, ' ');
        };

        const form = container.closest('form');
        if (form) {
            form.addEventListener('formdata', function () {
                hiddenInput.value = normalizeWhitespace(quill.getSemanticHTML());
            });
        }

        quill.on('text-change', function () {
            hiddenInput.value = normalizeWhitespace(quill.getSemanticHTML());
        });
    });
}
