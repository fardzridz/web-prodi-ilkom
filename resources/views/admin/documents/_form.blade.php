@php
    $isEditing = $document->exists;
    $selectedStatus = old('status', $document->status ?: 'draft');
    $selectedCategory = (string) old('document_category_id', $document->document_category_id);
    $storedFileExists = $isEditing && Storage::disk('public')->exists($document->file);
@endphp

<form
    class="document-form"
    action="{{ $isEditing ? route('admin.dokumen.update', $document) : route('admin.dokumen.store') }}"
    method="post"
    enctype="multipart/form-data"
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div class="admin-panel document-form-main">
        <div class="document-form-grid">
            <div class="activity-field document-title-field">
                <label for="document-title">Judul dokumen <span aria-hidden="true">*</span></label>
                <input
                    id="document-title"
                    name="title"
                    type="text"
                    value="{{ old('title', $document->title) }}"
                    maxlength="255"
                    required
                    autofocus
                    @error('title') aria-invalid="true" aria-describedby="document-title-error" @enderror
                >
                @error('title')<small id="document-title-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            <div class="activity-field">
                <label for="document-slug">Slug</label>
                <input
                    id="document-slug"
                    name="slug"
                    type="text"
                    value="{{ old('slug', $document->slug) }}"
                    maxlength="255"
                    placeholder="Otomatis dari judul"
                    @error('slug') aria-invalid="true" aria-describedby="document-slug-error" @enderror
                >
                <small>Kosongkan untuk membuat slug otomatis dari judul.</small>
                @error('slug')<small id="document-slug-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            <div class="activity-field document-description-field">
                <label for="document-description">Deskripsi</label>
                <textarea
                    id="document-description"
                    name="description"
                    rows="6"
                    maxlength="5000"
                    @error('description') aria-invalid="true" aria-describedby="document-description-error" @enderror
                >{{ old('description', $document->description) }}</textarea>
                <small>Maksimal 5.000 karakter.</small>
                @error('description')<small id="document-description-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            <div class="activity-field document-upload-field">
                <label for="document-file">
                    {{ $isEditing ? 'Ganti berkas' : 'Berkas dokumen' }}
                    @unless ($isEditing)<span aria-hidden="true">*</span>@endunless
                </label>
                <label class="document-upload-box" for="document-file">
                    <i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i>
                    <strong>{{ $isEditing ? 'Pilih berkas pengganti' : 'Pilih berkas untuk diunggah' }}</strong>
                    <span data-file-name-output>PDF, DOC, atau DOCX · maksimal 10 MB</span>
                </label>
                <input
                    id="document-file"
                    class="sr-only"
                    name="document_file"
                    type="file"
                    accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                    data-file-input
                    @unless ($isEditing) required @endunless
                    @error('document_file') aria-invalid="true" aria-describedby="document-file-error" @enderror
                >
                @error('document_file')<small id="document-file-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            @if ($isEditing)
                <div @class(['document-current-file', 'is-missing' => ! $storedFileExists])>
                    <span class="document-file-icon" aria-hidden="true">
                        <i class="fa-solid fa-file-{{ $document->file_type === 'pdf' ? 'pdf' : 'word' }}"></i>
                    </span>
                    <div>
                        <strong>Berkas saat ini: {{ $document->slug }}.{{ $document->file_type }}</strong>
                        <p>{{ $document->fileTypeLabel() }} · {{ $document->formattedFileSize() }}</p>
                        @if ($storedFileExists)
                            <a href="{{ route('admin.dokumen.download', $document) }}">Unduh berkas saat ini</a>
                        @else
                            <small>Berkas fisik tidak ditemukan. Unggah pengganti sebelum menyimpan.</small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <aside class="document-form-aside">
        <section class="admin-panel document-publish-panel">
            <h2>Pengaturan Dokumen</h2>

            <div class="activity-field">
                <label for="document-category">Kategori <span aria-hidden="true">*</span></label>
                <select id="document-category" name="document_category_id" required>
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($selectedCategory === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('document_category_id')<small class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            <div class="activity-field">
                <label for="document-status">Status <span aria-hidden="true">*</span></label>
                <select id="document-status" name="status" required>
                    <option value="draft" @selected($selectedStatus === 'draft')>Draft</option>
                    <option value="published" @selected($selectedStatus === 'published')>Published</option>
                </select>
                <small>Hanya dokumen published yang akan tampil pada halaman publik.</small>
                @error('status')<small class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            @if ($categories->isEmpty())
                <p class="document-category-warning">
                    Buat kategori terlebih dahulu sebelum menyimpan dokumen.
                </p>
            @endif

            <a class="document-category-link" href="{{ route('admin.kategori-dokumen.index') }}">
                <i class="fa-solid fa-folder-tree" aria-hidden="true"></i>
                Kelola Kategori
            </a>
        </section>

        <div class="document-form-actions">
            <a class="admin-button admin-button-secondary" href="{{ route('admin.dokumen.index') }}">Batal</a>
            <button class="admin-button admin-button-primary" type="submit" @disabled($categories->isEmpty())>
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Dokumen' }}
            </button>
        </div>
    </aside>
</form>
