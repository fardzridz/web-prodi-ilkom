@extends('layouts.admin')

@section('title', 'Akun Pengelola - Pengelola Situs Prodi')
@section('page-section', 'Pengaturan')
@section('page-heading', 'Akun Pengelola')
@section('page-helper', 'Atur identitas login dan kata sandi akun pengelola situs.')

@section('content')
    <form class="account-editor-form" action="{{ route('admin.akun-admin.update') }}" method="post">
        @csrf
        @method('PUT')

        <section class="admin-panel account-profile-card">
            <span class="account-profile-avatar" aria-hidden="true">{{ strtoupper(mb_substr($admin->name, 0, 2)) }}</span>
            <div>
                <p>Akun Aktif</p>
                <h2>{{ $admin->name }}</h2>
                <span>{{ $admin->email }}</span>
            </div>
            <strong><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Administrator</strong>
        </section>

        <div class="account-editor-grid">
            <div class="account-editor-main">
                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading">
                        <span aria-hidden="true"><i class="fa-solid fa-user-pen"></i></span>
                        <div><p>Identitas Login</p><h2>Informasi Akun</h2></div>
                    </div>

                    <div class="content-form-grid">
                        <div class="activity-field">
                            <label for="account-name">Nama lengkap <span aria-hidden="true">*</span></label>
                            <input id="account-name" name="name" type="text" maxlength="255" value="{{ old('name', $admin->name) }}" autocomplete="name" required @error('name') aria-invalid="true" @enderror>
                            @error('name')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field">
                            <label for="account-email">Alamat surel <span aria-hidden="true">*</span></label>
                            <input id="account-email" name="email" type="email" maxlength="255" value="{{ old('email', $admin->email) }}" autocomplete="email" required @error('email') aria-invalid="true" @enderror>
                            <small>Alamat ini digunakan ketika masuk ke panel pengelola.</small>
                            @error('email')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field content-field-full">
                            <label for="account-role">Peran</label>
                            <div class="account-role-field"><i class="fa-solid fa-user-shield" aria-hidden="true"></i><input id="account-role" type="text" value="Administrator" readonly aria-readonly="true"></div>
                            <small>Peran tidak dapat diubah dari halaman ini.</small>
                        </div>
                    </div>
                </section>

                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading">
                        <span aria-hidden="true"><i class="fa-solid fa-key"></i></span>
                        <div><p>Keamanan</p><h2>Ganti Kata Sandi</h2></div>
                    </div>

                    <div class="content-form-grid">
                        <div class="activity-field">
                            <label for="account-password">Kata sandi baru</label>
                            <input id="account-password" name="password" type="password" autocomplete="new-password" @error('password') aria-invalid="true" @enderror>
                            <small>Minimal 10 karakter, huruf besar-kecil, dan angka.</small>
                            @error('password')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field">
                            <label for="account-password-confirmation">Ulangi kata sandi baru</label>
                            <input id="account-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password">
                            <small>Kosongkan keduanya bila tidak ingin mengganti kata sandi.</small>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="account-security-panel">
                <section class="admin-panel account-confirm-card">
                    <div class="account-confirm-icon"><i class="fa-solid fa-lock" aria-hidden="true"></i></div>
                    <h2>Konfirmasi Perubahan</h2>
                    <p>Masukkan kata sandi saat ini untuk memastikan perubahan dilakukan oleh pemilik akun.</p>
                    <div class="activity-field">
                        <label for="account-current-password">Kata sandi saat ini <span aria-hidden="true">*</span></label>
                        <input id="account-current-password" name="current_password" type="password" autocomplete="current-password" required @error('current_password') aria-invalid="true" @enderror>
                        @error('current_password')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                    <button class="admin-button admin-button-primary" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan Akun</button>
                </section>

                <section class="account-security-note">
                    <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    <p>Setelah email atau kata sandi diubah, gunakan data terbaru saat login berikutnya.</p>
                </section>
            </aside>
        </div>
    </form>
@endsection
