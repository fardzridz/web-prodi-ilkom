@extends('layouts.admin')

@section('title', 'Akun Pengelola | Pengelola Situs Prodi')
@section('page-heading', 'Akun Pengelola')

@section('content')
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profil Akun</h3>

        <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ route('admin.akun-admin.update') }}" method="post">
            @csrf
            @method('PUT')

            <div class="mb-6 flex flex-col gap-5 sm:flex-row sm:items-center">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 text-2xl font-semibold text-white shrink-0">
                    {{ strtoupper(mb_substr($admin->name, 0, 2)) }}
                </span>
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $admin->name }}</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $admin->email }}</p>
                </div>
                <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-4 py-2 text-sm font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill=""/>
                    </svg>
                    Administrator
                </span>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Identitas Login</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi akun yang digunakan untuk masuk ke panel pengelola.</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="account-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Nama lengkap<span class="text-error-500">*</span>
                                    </label>
                                    <input id="account-name" name="name" type="text" maxlength="255"
                                        value="{{ old('name', $admin->name) }}" autocomplete="name" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    @error('name')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="account-email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Alamat surel<span class="text-error-500">*</span>
                                    </label>
                                    <input id="account-email" name="email" type="email" maxlength="255"
                                        value="{{ old('email', $admin->email) }}" autocomplete="email" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Digunakan saat masuk ke panel pengelola.</p>
                                    @error('email')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="account-role" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Peran</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-12-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill=""/>
                                            </svg>
                                        </span>
                                        <input id="account-role" type="text" value="Administrator" readonly
                                            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 pl-11 pr-4 py-2.5 text-sm text-gray-500 dark:border-gray-700 dark:bg-white/5 dark:text-gray-400" />
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Peran tidak dapat diubah dari halaman ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Keamanan</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ganti kata sandi akun pengelola.</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="account-password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kata sandi baru</label>
                                    <div x-data="{ showPassword: false }" class="relative">
                                        <input :type="showPassword ? 'text' : 'password'" id="account-password" name="password"
                                            autocomplete="new-password"
                                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                        <span @click="showPassword = !showPassword"
                                            class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                            <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3"/>
                                            </svg>
                                            <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709Z" fill="#98A2B3"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal 10 karakter, huruf besar-kecil, dan angka.</p>
                                    @error('password')
                                        <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="account-password-confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Ulangi kata sandi baru</label>
                                    <input id="account-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan keduanya bila tidak ingin mengganti kata sandi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" fill=""/>
                                </svg>
                            </div>
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Konfirmasi Perubahan</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Masukkan kata sandi saat ini untuk menyimpan perubahan.</p>
                        </div>
                        <div class="border-t border-gray-100 p-6 dark:border-gray-800">
                            <div class="mb-5">
                                <label for="account-current-password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Kata sandi saat ini<span class="text-error-500">*</span>
                                </label>
                                <div x-data="{ showCurrent: false }" class="relative">
                                    <input :type="showCurrent ? 'text' : 'password'" id="account-current-password" name="current_password"
                                        autocomplete="current-password" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    <span @click="showCurrent = !showCurrent"
                                        class="absolute top-1/2 right-4 z-30 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                        <svg x-show="!showCurrent" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3"/>
                                        </svg>
                                        <svg x-show="showCurrent" class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709Z" fill="#98A2B3"/>
                                        </svg>
                                    </span>
                                </div>
                                @error('current_password')
                                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit" :disabled="submitting"
                                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/>
                                </svg>
                                <x-admin.spinner x-show="submitting" />
                                Simpan Akun
                            </button>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-blue-light-200 bg-blue-light-50 p-5 dark:border-blue-light-500/20 dark:bg-blue-light-500/10">
                        <div class="flex items-start gap-3">
                            <div class="-mt-0.5 text-blue-light-500">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15v-6h2v6h-2zm0-8V7h2v2h-2z" fill=""/>
                                </svg>
                            </div>
                            <p class="text-sm text-blue-light-700 dark:text-blue-light-300">Setelah email atau kata sandi diubah, gunakan data terbaru saat login berikutnya.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
@endsection
