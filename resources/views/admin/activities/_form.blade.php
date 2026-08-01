@php
    $isEditing = $activity->exists;
    $selectedStatus = old('status', $activity->status ?: 'draft');
    $publishedAt = old('published_at', $activity->published_at?->format('Y-m-d\TH:i'));
@endphp

<form
    class="activity-form-layout"
    action="{{ $isEditing ? route('admin.kegiatan.update', $activity) : route('admin.kegiatan.store') }}"
    method="post"
    enctype="multipart/form-data"
    data-activity-form
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <section class="admin-panel activity-form-main">
        <div class="activity-field">
            <label for="activity-title">Judul kegiatan <span aria-hidden="true">*</span></label>
            <input
                id="activity-title"
                name="title"
                type="text"
                value="{{ old('title', $activity->title) }}"
                maxlength="255"
                required
                autofocus
                @error('title') aria-invalid="true" aria-describedby="activity-title-error" @enderror
            >
            @error('title')<small id="activity-title-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="activity-slug">Alamat halaman</label>
            <input
                id="activity-slug"
                name="slug"
                type="text"
                value="{{ old('slug', $activity->slug) }}"
                maxlength="255"
                placeholder="otomatis-dari-judul"
                @error('slug') aria-invalid="true" aria-describedby="activity-slug-error" @enderror
            >
            <small>Jika dikosongkan, slug dibuat otomatis dari judul.</small>
            @error('slug')<small id="activity-slug-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="activity-excerpt">Ringkasan</label>
            <textarea
                id="activity-excerpt"
                name="excerpt"
                rows="4"
                maxlength="1000"
                @error('excerpt') aria-invalid="true" aria-describedby="activity-excerpt-error" @enderror
            >{{ old('excerpt', $activity->excerpt) }}</textarea>
            <small>Maksimal 1.000 karakter untuk ringkasan kartu kegiatan.</small>
            @error('excerpt')<small id="activity-excerpt-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="activity-category">Kategori</label>
            <input
                id="activity-category"
                name="category"
                type="text"
                value="{{ old('category', $activity->category) }}"
                maxlength="100"
                placeholder="contoh: Seminar, Workshop, Kuliah Tamu"
                @error('category') aria-invalid="true" aria-describedby="activity-category-error" @enderror
            >
            @error('category')<small id="activity-category-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

            <div class="activity-field">
                <label for="activity-content">Isi kegiatan <span aria-hidden="true">*</span></label>
                <div id="activity-content" class="quill-editor"></div>
                <input id="activity-content-hidden" type="hidden" name="content" value="{{ old('content', $activity->content) }}">
                @error('content')<small id="activity-content-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>
    </section>

    <aside class="activity-form-aside">
        <section class="admin-panel activity-publish-panel">
            <div class="activity-field">
                <label for="activity-image">Gambar kegiatan</label>
                <label class="activity-image-upload" for="activity-image" data-image-upload>
                    <div class="activity-image-preview" data-image-preview @if (! $activity->image) data-empty @endif>
                        @if ($activity->image)
                            <img src="{{ asset('storage/'.$activity->image) }}" alt="Gambar kegiatan saat ini" data-image-preview-img>
                        @endif
                        <div class="activity-image-overlay">
                            <i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i>
                            <strong data-image-overlay-label>{{ $activity->image ? 'Ganti gambar' : 'Pilih gambar' }}</strong>
                        </div>
                    </div>
                    <input
                        id="activity-image"
                        class="sr-only"
                        name="image"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        data-image-input
                        @error('image') aria-invalid="true" aria-describedby="activity-image-error" @enderror
                    >
                    @error('image')<small id="activity-image-error" class="activity-field-error">{{ $message }}</small>@enderror
                </label>
                <small data-image-file-name>Format JPG, PNG, atau WebP · maksimal 2 MB</small>
            </div>

            <div class="activity-field">
                <label for="activity-date">Tanggal kegiatan <span aria-hidden="true">*</span></label>
                <input
                    id="activity-date"
                    name="activity_date"
                    type="date"
                    value="{{ old('activity_date', $activity->activity_date?->format('Y-m-d')) }}"
                    min="2000-01-01"
                    max="2100-12-31"
                    required
                    data-admin-date-picker
                    @error('activity_date') aria-invalid="true" aria-describedby="activity-date-error" @enderror
                >
                @error('activity_date')<small id="activity-date-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            <div class="activity-field">
                <label for="activity-location">Lokasi <span aria-hidden="true">*</span></label>
                <input
                    id="activity-location"
                    name="location"
                    type="text"
                    value="{{ old('location', $activity->location) }}"
                    maxlength="255"
                    required
                    @error('location') aria-invalid="true" aria-describedby="activity-location-error" @enderror
                >
                @error('location')<small id="activity-location-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            <div class="activity-field">
                <label for="activity-status">Status <span aria-hidden="true">*</span></label>
                <select id="activity-status" name="status" required data-activity-status>
                    <option value="draft" @selected($selectedStatus === 'draft')>Draf</option>
                    <option value="scheduled" @selected($selectedStatus === 'scheduled')>Terjadwal</option>
                    <option value="published" @selected($selectedStatus === 'published')>Terbit</option>
                </select>
                @error('status')<small class="activity-field-error">{{ $message }}</small>@enderror
            </div>

            <div class="activity-field" data-published-field @if ($selectedStatus !== 'scheduled') hidden @endif>
                <label for="activity-published-at">Jadwal tayang <span aria-hidden="true">*</span></label>
                <input
                    id="activity-published-at"
                    name="published_at"
                    type="datetime-local"
                    value="{{ $publishedAt }}"
                    min="{{ now()->format('Y-m-d\TH:i') }}"
                    @if ($selectedStatus === 'scheduled') required @endif
                    data-published-input
                    data-admin-date-picker
                    @error('published_at') aria-invalid="true" aria-describedby="activity-published-at-error" @enderror
                >
                <small>Wajib berada di masa depan untuk status terjadwal.</small>
                @error('published_at')<small id="activity-published-at-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>
        </section>

        <div class="activity-form-actions">
            <button class="admin-button admin-button-primary" type="submit">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}
            </button>
            <a class="admin-button admin-button-secondary" href="{{ route('admin.kegiatan.index') }}">Batal</a>
        </div>
    </aside>
</form>
