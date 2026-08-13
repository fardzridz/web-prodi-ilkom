@php
    $isEditing = $document->exists;
    $selectedStatus = old('status', $document->status ?: 'draft');
    $selectedCategory = (string) old('document_category_id', $document->document_category_id);
    $storedFileExists = $isEditing && Storage::disk('public')->exists($document->file);
@endphp

<form x-data="{ submitting: false }" @submit="submitting = true" action="{{ $isEditing ? route('admin.dokumen.update', $document) : route('admin.dokumen.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @if ($isEditing) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Informasi Dokumen</h4></div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="document-title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul dokumen<span class="text-error-500">*</span></label>
                            <input id="document-title" name="title" type="text" maxlength="255" value="{{ old('title', $document->title) }}" required autofocus
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            @error('title')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="document-slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Slug</label>
                                <input id="document-slug" name="slug" type="text" maxlength="255" value="{{ old('slug', $document->slug) }}" placeholder="Otomatis dari judul"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('slug')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="document-file" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $isEditing ? 'Ganti berkas' : 'Berkas' }}<span class="text-error-500">*</span></label>
                                <label for="document-file" class="flex items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-4 cursor-pointer hover:border-brand-400 dark:border-gray-700 dark:hover:border-brand-700 transition">
                                    <svg class="mr-2 fill-current text-gray-400" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z" fill=""/></svg>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">PDF, DOC, DOCX — maks 10 MB</span>
                                    <input id="document-file" name="document_file" type="file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="sr-only" @unless($isEditing) required @endunless />
                                </label>
                                @error('document_file')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="document-description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                            <textarea id="document-description" name="description" rows="5" maxlength="5000"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y">{{ old('description', $document->description) }}</textarea>
                            @error('description')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                @if ($isEditing)
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                                <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6z" fill=""/></svg>
                            </span>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">Berkas: {{ $document->slug }}.{{ $document->file_type }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $document->fileTypeLabel() }} · {{ $document->formattedFileSize() }}</p>
                                @if ($storedFileExists)
                                    <a href="{{ route('admin.dokumen.download', $document) }}" class="text-xs text-brand-500 hover:text-brand-600 dark:text-brand-400">Unduh berkas</a>
                                @else
                                    <p class="text-xs text-error-500">Berkas fisik tidak ditemukan. Unggah pengganti.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Pengaturan</h4></div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="document-category" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kategori<span class="text-error-500">*</span></label>
                            <x-admin.select id="document-category" name="document_category_id" placeholder="Pilih kategori" :selected="$selectedCategory" :options="$categories->mapWithKeys(fn ($c) => [(string) $c->id => $c->name])->all()" required />@error('document_category_id')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="document-status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status<span class="text-error-500">*</span></label>
                            <x-admin.select id="document-status" name="status" :selected="$selectedStatus" :options="['draft' => 'Draf', 'published' => 'Terbit']" required />
                            @error('status')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        @if ($categories->isEmpty())
                            <div class="rounded-lg bg-warning-50 p-3 text-sm text-warning-700 dark:bg-warning-500/10 dark:text-orange-400">Buat kategori terlebih dahulu sebelum menyimpan.</div>
                        @endif
                        <a href="{{ route('admin.kategori-dokumen.index') }}" class="inline-flex items-center gap-1.5 text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                            <svg class="fill-current" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2z" fill=""/></svg> Kelola Kategori
                        </a>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.dokumen.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">Batal</a>
                    <button type="submit" :disabled="submitting || {{ $categories->isEmpty() ? 'true' : 'false' }}" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition flex-1 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/></svg>
                        <x-admin.spinner x-show="submitting" />
                        {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Dokumen' }}
                    </button>
                </div>
            </div>
        </div>
    </form>
