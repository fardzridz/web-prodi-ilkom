@php
    $isEditing = $activity->exists;
    $selectedStatus = old('status', $activity->status ?: 'draft');
    $publishedAt = old('published_at', $activity->published_at?->format('Y-m-d\TH:i'));
@endphp

<form x-data="{ submitting: false, status: '{{ $selectedStatus }}' }" @submit="submitting = true" action="{{ $isEditing ? route('admin.kegiatan.update', $activity) : route('admin.kegiatan.store') }}" method="post" enctype="multipart/form-data" @select-change.window="if ($event.detail.name === 'status') status = $event.detail.value">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Informasi Kegiatan</h4></div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="activity-title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul kegiatan<span class="text-error-500">*</span></label>
                            <input id="activity-title" name="title" type="text" maxlength="255" value="{{ old('title', $activity->title) }}" required autofocus
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            @error('title')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="activity-slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat halaman</label>
                                <input id="activity-slug" name="slug" type="text" maxlength="255" value="{{ old('slug', $activity->slug) }}" placeholder="otomatis-dari-judul"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('slug')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="activity-category" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kategori</label>
                                <input id="activity-category" name="category" type="text" maxlength="100" value="{{ old('category', $activity->category) }}" placeholder="contoh: Seminar, Workshop"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('category')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="activity-excerpt" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ringkasan</label>
                            <textarea id="activity-excerpt" name="excerpt" rows="3" maxlength="1000"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y">{{ old('excerpt', $activity->excerpt) }}</textarea>
                            @error('excerpt')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Isi kegiatan<span class="text-error-500">*</span></label>
                            <div id="activity-content" class="quill-editor"></div>
                            <input id="activity-content-hidden" type="hidden" name="content" value="{{ old('content', $activity->content) }}">
                            @error('content')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Media & Detail</h4></div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Gambar kegiatan</label>
                            <x-admin.image-upload id="activity-image" name="image"
                                :existing-src="$activity->image ? asset('storage/'.$activity->image) : null"
                                label="Upload gambar"
                                help-text="JPG, PNG, WebP — maks 2 MB"
                                preview-class="max-w-full h-40 rounded-lg mx-auto object-cover" />
                            @error('image')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="activity-date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal<span class="text-error-500">*</span></label>
                                <input id="activity-date" name="activity_date" type="date" value="{{ old('activity_date', $activity->activity_date?->format('Y-m-d')) }}" min="2000-01-01" max="2100-12-31" required
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('activity_date')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="activity-location" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Lokasi<span class="text-error-500">*</span></label>
                                <input id="activity-location" name="location" type="text" maxlength="255" value="{{ old('location', $activity->location) }}" required
                                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('location')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="activity-status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status<span class="text-error-500">*</span></label>
                            <x-admin.select id="activity-status" name="status" :selected="$selectedStatus" :options="['draft' => 'Draf', 'scheduled' => 'Terjadwal', 'published' => 'Terbit']" required />
                            @error('status')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div x-show="status === 'scheduled'">
                            <label for="activity-published-at" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jadwal tayang<span class="text-error-500">*</span></label>
                            <input id="activity-published-at" name="published_at" type="datetime-local" value="{{ $publishedAt }}" @unless($isEditing) min="{{ now()->format('Y-m-d\TH:i') }}" @endunless
                                x-bind:required="status === 'scheduled'"
                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            @error('published_at')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.kegiatan.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">Batal</a>
                    <button type="submit" :disabled="submitting" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition flex-1 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/></svg>
                        <x-admin.spinner x-show="submitting" />
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Kegiatan' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
