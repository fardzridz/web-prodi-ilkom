@php
    $isEditing = $alumni->exists;
    $selectedStatus = old('status', $alumni->status ?: 'active');
    $hasStoredPhoto = filled($alumni->photo) && Storage::disk('public')->exists($alumni->photo);
    $maximumYear = now()->addYear()->year;
@endphp

<form x-data="{ submitting: false }" @submit="submitting = true" action="{{ $isEditing ? route('admin.alumni.update', $alumni) : route('admin.alumni.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                        <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Informasi Alumni</h4>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="alumni-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama lengkap<span class="text-error-500">*</span>
                                </label>
                                <input id="alumni-name" name="name" type="text" maxlength="255"
                                    value="{{ old('name', $alumni->name) }}" required autofocus
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('name')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="alumni-batch-year" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tahun angkatan<span class="text-error-500">*</span>
                                </label>
                                <input id="alumni-batch-year" name="batch_year" type="number" min="1950" max="{{ $maximumYear }}" inputmode="numeric"
                                    value="{{ old('batch_year', $alumni->batch_year) }}" required
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('batch_year')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="alumni-graduation-year" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tahun lulus</label>
                                <input id="alumni-graduation-year" name="graduation_year" type="number" min="1950" max="{{ $maximumYear }}" inputmode="numeric"
                                    value="{{ old('graduation_year', $alumni->graduation_year) }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Boleh dikosongkan.</p>
                                @error('graduation_year')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="alumni-job-position" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Posisi pekerjaan</label>
                                <input id="alumni-job-position" name="job_position" type="text" maxlength="255"
                                    value="{{ old('job_position', $alumni->job_position) }}" placeholder="Contoh: Web Developer"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('job_position')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="alumni-company" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Instansi atau perusahaan</label>
                                <input id="alumni-company" name="company" type="text" maxlength="255"
                                    value="{{ old('company', $alumni->company) }}" placeholder="Contoh: PT Teknologi"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('company')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="alumni-status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Status tampil<span class="text-error-500">*</span>
                                </label>
                                <x-admin.select id="alumni-status" name="status" :selected="$selectedStatus" :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" required />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hanya alumni aktif yang tampil di halaman publik.</p>
                                @error('status')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                        <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Testimoni</h4>
                    </div>
                    <div class="p-6">
                        <label for="alumni-testimonial" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Testimoni alumni</label>
                        <textarea id="alumni-testimonial" name="testimonial" rows="6" maxlength="5000"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y">{{ old('testimonial', $alumni->testimonial) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 5.000 karakter.</p>
                        @error('testimonial')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                        <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Foto Alumni</h4>
                    </div>
                    <div class="p-6">
                        <x-admin.image-upload id="alumni-photo" name="photo"
                            :existing-src="$hasStoredPhoto ? Storage::disk('public')->url($alumni->photo) : null"
                            label="Upload foto"
                            help-text="JPG, PNG, GIF, WebP — maks 2 MB"
                            preview-class="max-h-48 rounded-full mx-auto" />
                        @error('photo')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.alumni.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                        Batal
                    </a>
                    <button type="submit" :disabled="submitting"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition flex-1 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/>
                        </svg>
                        <x-admin.spinner x-show="submitting" />
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Alumni' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
