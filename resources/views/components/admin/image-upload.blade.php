@props([
    'id',
    'name',
    'existingSrc' => null,
    'accept' => 'image/jpeg,image/png,image/gif,image/webp',
    'label' => 'Upload gambar',
    'helpText' => 'JPG, PNG, GIF, WebP — maks 2 MB',
    'previewClass' => 'max-h-48 rounded-lg mx-auto',
])

@php $hasExisting = filled($existingSrc); @endphp

<div
    id="{{ $id }}-preview"
    class="max-w-sm mx-auto p-6 bg-gray-100 dark:bg-gray-800 border-dashed border-2 border-gray-400 dark:border-gray-600 rounded-lg text-center cursor-pointer {{ $hasExisting ? 'border-0 bg-transparent dark:bg-transparent p-0' : '' }}"
    data-image-upload
    data-image-upload-has-existing="{{ $hasExisting ? 'true' : 'false' }}"
    data-image-upload-label="{{ $label }}"
    data-image-upload-help="{{ $helpText }}"
    data-image-upload-preview-class="{{ $previewClass }}"
>
    <input id="{{ $id }}" name="{{ $name }}" type="file" class="hidden" accept="{{ $accept }}" />
    <label for="{{ $id }}" class="cursor-pointer">
        @if ($hasExisting)
            <img src="{{ $existingSrc }}" class="{{ $previewClass }}" alt="" />
            <span id="{{ $id }}-filename" class="text-gray-500 dark:text-gray-400 text-sm block mt-2">Klik untuk mengganti</span>
            @if ($helpText)
                <p class="font-normal text-sm text-gray-400 dark:text-gray-500 md:px-6 mt-1">{!! $helpText !!}</p>
            @endif
        @else
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-700 dark:text-gray-400 mx-auto mb-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-700 dark:text-gray-300">{{ $label }}</h5>
            <span id="{{ $id }}-filename" class="text-gray-500 dark:text-gray-400 text-sm"></span>
            @if ($helpText)
                <p class="font-normal text-sm text-gray-400 dark:text-gray-500 md:px-6">{!! $helpText !!}</p>
            @endif
        @endif
    </label>
</div>

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-image-upload]').forEach(function(preview) {
                const input = preview.querySelector('input[type="file"]');
                if (!input) return;

                const label = preview.dataset.imageUploadLabel;
                const helpText = preview.dataset.imageUploadHelp;
                const previewClass = preview.dataset.imageUploadPreviewClass;
                let listenerAdded = false;

                if (preview.dataset.imageUploadHasExisting === 'true') {
                    preview.addEventListener('click', function() { input.click(); });
                    listenerAdded = true;
                }

                function renderEmpty() {
                    let html = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-700 dark:text-gray-400 mx-auto mb-4">'
                        + '<path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>'
                        + '<h5 class="mb-2 text-xl font-bold tracking-tight text-gray-700 dark:text-gray-300">' + label + '</h5>'
                        + '<span id="' + input.id + '-filename" class="text-gray-500 dark:text-gray-400 text-sm"></span>';
                    if (helpText) {
                        html += '<p class="font-normal text-sm text-gray-400 dark:text-gray-500 md:px-6">' + helpText + '</p>';
                    }
                    preview.querySelector('label').innerHTML = html;
                    preview.classList.add('border-dashed', 'border-2', 'border-gray-400', 'dark:border-gray-600', 'p-6', 'bg-gray-100', 'dark:bg-gray-800');
                    preview.classList.remove('border-0', 'bg-transparent', 'dark:bg-transparent', 'p-0');
                }

                function renderPreview(src, filename) {
                    let html = '<img src="' + src + '" class="' + previewClass + '" alt="Image preview" />'
                        + '<span id="' + input.id + '-filename" class="text-gray-500 dark:text-gray-400 text-sm block mt-2">' + filename + '</span>';
                    if (helpText) {
                        html += '<p class="font-normal text-sm text-gray-400 dark:text-gray-500 md:px-6 mt-1">' + helpText + '</p>';
                    }
                    preview.querySelector('label').innerHTML = html;
                    preview.classList.remove('border-dashed', 'border-2', 'border-gray-400', 'dark:border-gray-600', 'p-6', 'bg-gray-100', 'dark:bg-gray-800');
                    preview.classList.add('border-0', 'bg-transparent', 'dark:bg-transparent', 'p-0');
                }

                input.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            renderPreview(e.target.result, file.name);
                        };
                        reader.readAsDataURL(file);

                        if (!listenerAdded) {
                            preview.addEventListener('click', function() { input.click(); });
                            listenerAdded = true;
                        }
                    } else {
                        renderEmpty();
                        listenerAdded = false;
                    }
                });

                input.addEventListener('click', function(event) { event.stopPropagation(); });
            });
        });
    </script>
    @endpush
@endonce
