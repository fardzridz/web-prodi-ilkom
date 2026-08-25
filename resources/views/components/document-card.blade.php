@props(['document'])

@php
    $fileType = strtoupper((string) ($document['file_type'] ?? ''));
    $isPdf = $fileType === 'PDF';
    $isWord = in_array($fileType, ['DOC', 'DOCX'], true);
@endphp

<article class="dokumen-card card-pill group relative flex flex-col rounded-xl border border-line bg-white p-6" data-reveal>
    <div @class([
        'dokumen-icon flex h-14 w-14 shrink-0 items-center justify-center rounded-xl',
        'bg-red-50 text-red-600' => $isPdf,
        'bg-blue-50 text-blue-600' => $isWord,
        'bg-gray-100 text-gray-600' => ! $isPdf && ! $isWord,
    ])>
        @if ($isWord)
            <svg xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 640 640"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(30, 138, 255)" d="M192 112L304 112L304 200C304 239.8 336.2 272 376 272L464 272L464 512C464 520.8 456.8 528 448 528L192 528C183.2 528 176 520.8 176 512L176 128C176 119.2 183.2 112 192 112zM352 131.9L444.1 224L376 224C362.7 224 352 213.3 352 200L352 131.9zM192 64C156.7 64 128 92.7 128 128L128 512C128 547.3 156.7 576 192 576L448 576C483.3 576 512 547.3 512 512L512 250.5C512 233.5 505.3 217.2 493.3 205.2L370.7 82.7C358.7 70.7 342.5 64 325.5 64L192 64zM263.3 338.2C260.1 325.3 247.1 317.5 234.2 320.7C221.3 323.9 213.5 337 216.7 349.8L248.7 477.8C251.2 488 260.1 495.3 270.6 495.9C281.1 496.5 290.7 490.2 294.4 480.4L319.9 412.3L345.4 480.4C349.1 490.2 358.7 496.5 369.2 495.9C379.7 495.3 388.6 488 391.1 477.8L423.1 349.8C426.3 336.9 418.5 323.9 405.6 320.7C392.7 317.5 379.7 325.3 376.5 338.2L363.2 391.4L342.3 335.6C339 326.2 330 320 320 320C310 320 301 326.2 297.5 335.6L276.6 391.4L263.3 338.2z"/></svg>
        @elseif ($isPdf)
            <svg xmlns="http://www.w3.org/2000/svg" height="32" width="32" viewBox="0 0 640 640"><!--!Font Awesome Free v7.3.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2026 Fonticons, Inc.--><path fill="rgb(255, 30, 30)" d="M240 112L128 112C119.2 112 112 119.2 112 128L112 512C112 520.8 119.2 528 128 528L208 528L208 576L128 576C92.7 576 64 547.3 64 512L64 128C64 92.7 92.7 64 128 64L261.5 64C278.5 64 294.8 70.7 306.8 82.7L429.3 205.3C441.3 217.3 448 233.6 448 250.6L448 400.1L400 400.1L400 272.1L312 272.1C272.2 272.1 240 239.9 240 200.1L240 112.1zM380.1 224L288 131.9L288 200C288 213.3 298.7 224 312 224L380.1 224zM272 444L304 444C337.1 444 364 470.9 364 504C364 537.1 337.1 564 304 564L292 564L292 592C292 603 283 612 272 612C261 612 252 603 252 592L252 464C252 453 261 444 272 444zM304 524C315 524 324 515 324 504C324 493 315 484 304 484L292 484L292 524L304 524zM400 444L432 444C460.7 444 484 467.3 484 496L484 560C484 588.7 460.7 612 432 612L400 612C389 612 380 603 380 592L380 464C380 453 389 444 400 444zM432 572C438.6 572 444 566.6 444 560L444 496C444 489.4 438.6 484 432 484L420 484L420 572L432 572zM508 464C508 453 517 444 528 444L576 444C587 444 596 453 596 464C596 475 587 484 576 484L548 484L548 508L576 508C587 508 596 517 596 528C596 539 587 548 576 548L548 548L548 592C548 603 539 612 528 612C517 612 508 603 508 592L508 464z"/></svg>
        @else
            <svg class="h-9 w-9" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor" aria-hidden="true"><path d="M0 64C0 28.7 28.7 0 64 0L224 0l0 128c0 17.7 14.3 32 32 32l128 0 0 288c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64zm384 64l-128 0L256 0 384 128z"/></svg>
        @endif
    </div>
    <div class="dokumen-body mt-4 flex-1">
        @if($document['category'])
        <span class="dokumen-type inline-block text-xs font-semibold uppercase tracking-widest text-gold-deep">{{ $document['category'] }}</span>
        @endif
        <h3 class="dokumen-title mt-1 font-display text-lg font-bold leading-snug text-primary">{{ $document['title'] }}</h3>
        @if($document['description'])
        <p class="dokumen-desc mt-2 text-sm leading-relaxed text-muted line-clamp-2">{{ $document['description'] }}</p>
        @endif
    </div>
    <div class="dokumen-meta mt-4 flex flex-col gap-1.5 text-xs text-muted">
        @if($document['updated_label'])
        <span class="inline-flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            Diperbarui {{ $document['updated_label'] }}
        </span>
        @endif
        <span class="inline-flex items-center gap-1.5">
            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
            {{ $document['file_type'] }}{{ $document['file_size'] ? ' - '.$document['file_size'] : '' }}
        </span>
    </div>
    <div class="dokumen-actions mt-auto flex flex-wrap items-center gap-2.5 pt-6">
        <a href="{{ route('documents.download', $document['id']) }}" class="btn btn-primary btn-sm" download>
            <span class="btn-label">Unduh</span>
            <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        </a>
        <button type="button"
                class="btn btn-outline btn-sm doc-preview-trigger"
                data-preview-url="{{ route('documents.view', $document['id']) }}"
                data-preview-title="{{ $document['title'] }}"
                data-preview-type="{{ $document['file_type'] }}"
                data-download-url="{{ route('documents.download', $document['id']) }}">
            <span class="btn-label">Lihat</span>
            <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
        </button>
    </div>
</article>
