@extends('layouts.admin')

@section('title', 'Kontak | Pengelola Situs Prodi')
@section('page-heading', 'Kontak')

@section('content')
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Kontak Resmi Program Studi</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pastikan informasi yang disimpan merupakan kanal resmi.</p>
            </div>
            @if ($contact->exists && $contact->updated_at)
                <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">
                    Terakhir diperbarui {{ $contact->updated_at->translatedFormat('d F Y, H:i') }} WIB
                </span>
            @endif
        </div>

        <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ route('admin.kontak.update') }}" method="post">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Kontak Utama</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Lokasi dan komunikasi resmi prodi.</p>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label for="contact-address" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Alamat kampus<span class="text-error-500">*</span>
                                </label>
                                <textarea id="contact-address" name="address" rows="5" maxlength="5000" required
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-y">{{ old('address', $contact->address) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ mb_strlen($contact->address) }} / 5.000 karakter</p>
                                @error('address')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="contact-email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Surel<span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill=""/>
                                            </svg>
                                        </span>
                                        <input id="contact-email" name="email" type="email" maxlength="255" value="{{ old('email', $contact->email) }}" required
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-11 pr-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    </div>
                                    @error('email')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="contact-phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nomor telepon<span class="text-error-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <path d="M6.62 10.79c1.44 2.83 3.76 5.15 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill=""/>
                                            </svg>
                                        </span>
                                        <input id="contact-phone" name="phone" type="tel" maxlength="30" value="{{ old('phone', $contact->phone) }}" placeholder="+62 821-4155-4377" required
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-11 pr-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    </div>
                                    @error('phone')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Media Sosial</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kanal digital resmi program studi.</p>
                        </div>
                        <div class="p-6 space-y-5">
                            @php
                                $socials = [
                                    ['id' => 'instagram', 'label' => 'Instagram', 'placeholder' => 'https://instagram.com/akun'],
                                    ['id' => 'facebook', 'label' => 'Facebook', 'placeholder' => 'https://facebook.com/halaman'],
                                    ['id' => 'youtube', 'label' => 'YouTube', 'placeholder' => 'https://youtube.com/@kanal'],
                                ];
                            @endphp
                            @foreach ($socials as $social)
                                <div>
                                    <label for="contact-{{ $social['id'] }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ $social['label'] }}</label>
                                    <input id="contact-{{ $social['id'] }}" name="{{ $social['id'] }}" type="url" maxlength="2048"
                                        value="{{ old($social['id'], $contact->{$social['id']}) }}" placeholder="{{ $social['placeholder'] }}"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    @error($social['id'])<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Sematan Google Maps</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">URL atau kode iframe dari Google Maps.</p>
                        </div>
                        <div class="p-6">
                            <textarea id="contact-map-embed" name="map_embed" rows="7" maxlength="5000"
                                placeholder="Tempel kode iframe dari menu Bagikan → Sematkan peta"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-y">{{ old('map_embed', $contact->map_embed) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Sistem hanya menyimpan URL HTTPS dari atribut src.</p>
                            @error('map_embed')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Pratinjau Kontak</h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-start gap-3">
                                <svg class="fill-current shrink-0 mt-0.5 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill=""/>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->address ?: 'Alamat belum diatur' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="fill-current shrink-0 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill=""/>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->email ?: 'Surel belum diatur' }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="fill-current shrink-0 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M6.62 10.79c1.44 2.83 3.76 5.15 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" fill=""/>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->phone ?: 'Telepon belum diatur' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Pratinjau Peta</h4>
                        </div>
                        <div class="p-6">
                            @if ($contact->map_embed)
                                <iframe src="{{ $contact->map_embed }}" class="w-full rounded-lg h-48" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                            @else
                                <div class="flex flex-col items-center justify-center py-8 text-center">
                                    <svg class="fill-current mb-3 text-gray-300 dark:text-gray-600" width="40" height="40" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill=""/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-400 dark:text-gray-500">Lokasi Kampus</p>
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Peta akan tampil setelah URL disimpan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="submit" :disabled="submitting"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/>
                        </svg>
                        <x-admin.spinner x-show="submitting" />
                        Simpan Kontak
                    </button>
                </div>
            </div>
        </form>
@endsection
