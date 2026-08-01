@php
    $isEditing = $alumni->exists;
    $selectedStatus = old('status', $alumni->status ?: 'active');
    $hasStoredPhoto = filled($alumni->photo) && Storage::disk('public')->exists($alumni->photo);
    $maximumYear = now()->addYear()->year;
@endphp

<form
    class="admin-panel alumni-form"
    action="{{ $isEditing ? route('admin.alumni.update', $alumni) : route('admin.alumni.store') }}"
    method="post"
    enctype="multipart/form-data"
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div class="alumni-form-grid">
        <div class="activity-field">
            <label for="alumni-name">Nama lengkap <span aria-hidden="true">*</span></label>
            <input
                id="alumni-name"
                name="name"
                type="text"
                value="{{ old('name', $alumni->name) }}"
                maxlength="255"
                required
                autofocus
                @error('name') aria-invalid="true" aria-describedby="alumni-name-error" @enderror
            >
            @error('name')<small id="alumni-name-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="alumni-batch-year">Tahun angkatan <span aria-hidden="true">*</span></label>
            <input
                id="alumni-batch-year"
                name="batch_year"
                type="number"
                value="{{ old('batch_year', $alumni->batch_year) }}"
                min="1950"
                max="{{ $maximumYear }}"
                inputmode="numeric"
                required
                @error('batch_year') aria-invalid="true" aria-describedby="alumni-batch-year-error" @enderror
            >
            @error('batch_year')<small id="alumni-batch-year-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="alumni-graduation-year">Tahun lulus</label>
            <input
                id="alumni-graduation-year"
                name="graduation_year"
                type="number"
                value="{{ old('graduation_year', $alumni->graduation_year) }}"
                min="1950"
                max="{{ $maximumYear }}"
                inputmode="numeric"
                @error('graduation_year') aria-invalid="true" aria-describedby="alumni-graduation-year-error" @enderror
            >
            <small>Boleh dikosongkan jika belum lulus.</small>
            @error('graduation_year')<small id="alumni-graduation-year-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="alumni-status">Status tampil <span aria-hidden="true">*</span></label>
            <select id="alumni-status" name="status" required>
                <option value="active" @selected($selectedStatus === 'active')>Aktif</option>
                <option value="inactive" @selected($selectedStatus === 'inactive')>Nonaktif</option>
            </select>
            <small>Hanya alumni aktif yang akan ditampilkan pada halaman publik.</small>
            @error('status')<small class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="alumni-job-position">Posisi pekerjaan</label>
            <input
                id="alumni-job-position"
                name="job_position"
                type="text"
                value="{{ old('job_position', $alumni->job_position) }}"
                maxlength="255"
                placeholder="Contoh: Web Developer"
                @error('job_position') aria-invalid="true" aria-describedby="alumni-job-position-error" @enderror
            >
            @error('job_position')<small id="alumni-job-position-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="alumni-company">Instansi atau perusahaan</label>
            <input
                id="alumni-company"
                name="company"
                type="text"
                value="{{ old('company', $alumni->company) }}"
                maxlength="255"
                placeholder="Contoh: Studio Teknologi Pasuruan"
                @error('company') aria-invalid="true" aria-describedby="alumni-company-error" @enderror
            >
            @error('company')<small id="alumni-company-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="activity-field alumni-photo-field">
        <label for="alumni-photo">{{ $isEditing ? 'Ganti foto alumni' : 'Unggah foto alumni' }}</label>
        <label class="activity-image-upload" for="alumni-photo" data-image-upload>
            <div class="activity-image-preview" data-image-preview @if (! $hasStoredPhoto) data-empty @endif>
                @if ($hasStoredPhoto)
                    <img src="{{ Storage::disk('public')->url($alumni->photo) }}" alt="Foto {{ $alumni->name }}" data-image-preview-img>
                @endif
                <div class="activity-image-overlay">
                    <i class="fa-solid fa-user-graduate" aria-hidden="true"></i>
                    <strong data-image-overlay-label>{{ $hasStoredPhoto ? 'Ganti foto' : 'Pilih foto' }}</strong>
                </div>
            </div>
            <input
                id="alumni-photo"
                class="sr-only"
                name="photo"
                type="file"
                accept="image/jpeg,image/png,image/gif,image/webp"
                data-image-input
                @error('photo') aria-invalid="true" aria-describedby="alumni-photo-error" @enderror
            >
            @error('photo')<small id="alumni-photo-error" class="activity-field-error">{{ $message }}</small>@enderror
        </label>
        <small data-image-file-name>Format JPG, PNG, GIF, atau WebP · maksimal 2 MB</small>
    </div>

    <div class="activity-field alumni-testimonial-field">
        <label for="alumni-testimonial">Testimoni</label>
        <textarea
            id="alumni-testimonial"
            name="testimonial"
            rows="6"
            maxlength="5000"
            @error('testimonial') aria-invalid="true" aria-describedby="alumni-testimonial-error" @enderror
        >{{ old('testimonial', $alumni->testimonial) }}</textarea>
        <small>Maksimal 5.000 karakter.</small>
        @error('testimonial')<small id="alumni-testimonial-error" class="activity-field-error">{{ $message }}</small>@enderror
    </div>

    <div class="alumni-form-actions">
        <a class="admin-button admin-button-secondary" href="{{ route('admin.alumni.index') }}">Batal</a>
        <button class="admin-button admin-button-primary" type="submit">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
            {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Alumni' }}
        </button>
    </div>
</form>
