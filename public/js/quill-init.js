(function () {
    if (typeof Quill === 'undefined') return;

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.quill-editor').forEach(function (container) {
            var hiddenInput = document.getElementById(container.id + '-hidden');
            if (!hiddenInput) return;

            var quill = new Quill(container, {
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

            var existingContent = hiddenInput.value.trim();
            if (existingContent) {
                quill.clipboard.dangerouslyPasteHTML(existingContent);
            }

            var form = container.closest('form');
            if (form) {
                form.addEventListener('formdata', function () {
                    hiddenInput.value = quill.getSemanticHTML();
                });
            }

            quill.on('text-change', function () {
                hiddenInput.value = quill.getSemanticHTML();
            });
        });
    });
})();
