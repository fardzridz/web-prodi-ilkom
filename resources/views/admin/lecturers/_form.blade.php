@php
    $isEditing = $lecturer->exists;
    $selectedStatus = old('status', $lecturer->status ?: 'active');
    $hasStoredPhoto = filled($lecturer->photo) && Storage::disk('public')->exists($lecturer->photo);
@endphp

<form
    class="admin-panel lecturer-form"
    action="{{ $isEditing ? route('admin.dosen.update', $lecturer) : route('admin.dosen.store') }}"
    method="post"
    enctype="multipart/form-data"
>
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div class="lecturer-form-grid">
        <div class="activity-field">
            <label for="lecturer-name">Nama lengkap <span aria-hidden="true">*</span></label>
            <input
                id="lecturer-name"
                name="name"
                type="text"
                value="{{ old('name', $lecturer->name) }}"
                maxlength="255"
                required
                autofocus
                @error('name') aria-invalid="true" aria-describedby="lecturer-name-error" @enderror
            >
            @error('name')<small id="lecturer-name-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="lecturer-nidn">NIDN <span aria-hidden="true">*</span></label>
            <input
                id="lecturer-nidn"
                name="nidn"
                type="text"
                inputmode="numeric"
                value="{{ old('nidn', $lecturer->nidn) }}"
                minlength="8"
                maxlength="20"
                pattern="[0-9]{8,20}"
                required
                @error('nidn') aria-invalid="true" aria-describedby="lecturer-nidn-error" @enderror
            >
            <small>Gunakan 8 sampai 20 angka tanpa spasi atau tanda baca.</small>
            @error('nidn')<small id="lecturer-nidn-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="lecturer-position">Jabatan</label>
            <input
                id="lecturer-position"
                name="position"
                type="text"
                value="{{ old('position', $lecturer->position) }}"
                maxlength="255"
                @error('position') aria-invalid="true" aria-describedby="lecturer-position-error" @enderror
            >
            @error('position')<small id="lecturer-position-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="lecturer-expertise">Bidang keahlian</label>
            <input
                id="lecturer-expertise"
                name="expertise"
                type="text"
                value="{{ old('expertise', $lecturer->expertise) }}"
                maxlength="255"
                placeholder="Contoh: Sistem Cerdas, Data Mining"
                @error('expertise') aria-invalid="true" aria-describedby="lecturer-expertise-error" @enderror
            >
            @error('expertise')<small id="lecturer-expertise-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="lecturer-education">Pendidikan terakhir</label>
            <input
                id="lecturer-education"
                name="education"
                type="text"
                value="{{ old('education', $lecturer->education) }}"
                maxlength="255"
                @error('education') aria-invalid="true" aria-describedby="lecturer-education-error" @enderror
            >
            @error('education')<small id="lecturer-education-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="lecturer-email">Alamat surel</label>
            <input
                id="lecturer-email"
                name="email"
                type="email"
                value="{{ old('email', $lecturer->email) }}"
                maxlength="255"
                autocomplete="email"
                @error('email') aria-invalid="true" aria-describedby="lecturer-email-error" @enderror
            >
            @error('email')<small id="lecturer-email-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="lecturer-status">Status tampil <span aria-hidden="true">*</span></label>
            <select id="lecturer-status" name="status" required>
                <option value="active" @selected($selectedStatus === 'active')>Aktif</option>
                <option value="inactive" @selected($selectedStatus === 'inactive')>Nonaktif</option>
            </select>
            <small>Hanya dosen aktif yang akan ditampilkan pada halaman publik.</small>
            @error('status')<small class="activity-field-error">{{ $message }}</small>@enderror
        </div>

        <div class="activity-field">
            <label for="lecturer-sort-order">Urutan tampil <span aria-hidden="true">*</span></label>
            <input
                id="lecturer-sort-order"
                name="sort_order"
                type="number"
                value="{{ old('sort_order', $lecturer->sort_order ?? 0) }}"
                min="0"
                max="9999"
                required
                @error('sort_order') aria-invalid="true" aria-describedby="lecturer-sort-order-error" @enderror
            >
            <small>Angka lebih kecil akan tampil lebih dahulu.</small>
            @error('sort_order')<small id="lecturer-sort-order-error" class="activity-field-error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="lecturer-photo-panel">
        @if ($hasStoredPhoto)
            <img
                class="lecturer-photo-preview"
                src="{{ Storage::disk('public')->url($lecturer->photo) }}"
                alt="Foto {{ $lecturer->name }}"
                width="88"
                height="88"
            >
        @else
            <span class="lecturer-photo-preview lecturer-avatar-placeholder" aria-hidden="true">
                <i class="fa-solid fa-user" aria-hidden="true"></i>
            </span>
        @endif
        <div>
            <strong>Foto dosen</strong>
            <p>Slot foto sudah tersedia. Upload fisik diaktifkan.</p>
            <small>Task 22 (unggah foto) &middot; Task 23 (penyimpanan file fisik)</small>
            @if ($lecturer->photo)
                <small>Path saat ini: {{ $lecturer->photo }}</small>
            @endif
        </div>
    </div>

    <div class="activity-field lecturer-photo-field">
        <label for="lecturer-photo">{{ $isEditing ? 'Ganti foto dosen' : 'Unggah foto dosen' }}</label>
        <input
            id="lecturer-photo"
            name="photo"
            type="file"
            accept="image/jpeg,image/png,image/gif,image/webp"
            @error('photo') aria-invalid="true" aria-describedby="lecturer-photo-error" @enderror
        >
        <small>Format gambar (JPG, PNG, GIF, WebP), maksimal 2 MB. Disimpan ke storage/app/public/uploads/lecturers/.</small>
        @error('photo')<small id="lecturer-photo-error" class="activity-field-error">{{ $message }}</small>@enderror
    </div>

    <div class="activity-field lecturer-bio-field">
        <label for="lecturer-bio">Biografi singkat</label>
        <textarea
            id="lecturer-bio"
            name="bio"
            rows="6"
            maxlength="5000"
            @error('bio') aria-invalid="true" aria-describedby="lecturer-bio-error" @enderror
        >{{ old('bio', $lecturer->bio) }}</textarea>
        <small>Maksimal 5.000 karakter.</small>
        @error('bio')<small id="lecturer-bio-error" class="activity-field-error">{{ $message }}</small>@enderror
    </div>

    <div class="lecturer-form-actions">
        <a class="admin-button admin-button-secondary" href="{{ route('admin.dosen.index') }}">Batal</a>
        <button class="admin-button admin-button-primary" type="submit">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
            {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Dosen' }}
        </button>
    </div>
</form>
