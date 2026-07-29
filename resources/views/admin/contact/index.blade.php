@extends('layouts.admin')

@section('title', 'Kontak - Pengelola Situs Prodi')
@section('page-section', 'Pengaturan')
@section('page-heading', 'Kontak')
@section('page-helper', 'Atur alamat, kanal komunikasi, media sosial, dan peta lokasi kampus.')

@section('content')
    <form class="content-editor-form" action="{{ route('admin.kontak.update') }}" method="post">
        @csrf
        @method('PUT')

        <section class="admin-panel content-editor-intro">
            <span aria-hidden="true"><i class="fa-solid fa-address-book"></i></span>
            <div>
                <h2>Kontak Resmi Program Studi</h2>
                <p>Pastikan informasi yang disimpan merupakan kanal resmi. Tautan media sosial dan Google Maps akan divalidasi berdasarkan domainnya.</p>
            </div>
            <small>{{ $contact->exists && $contact->updated_at ? 'Terakhir diperbarui '.$contact->updated_at->translatedFormat('d F Y, H:i').' WIB' : 'Kontak belum pernah disimpan' }}</small>
        </section>

        <div class="contact-editor-layout">
            <div class="contact-editor-main">
                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading">
                        <span aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
                        <div><p>Lokasi dan Komunikasi</p><h2>Kontak Utama</h2></div>
                    </div>

                    <div class="content-form-grid">
                        <div class="activity-field content-field-full">
                            <label for="contact-address">Alamat kampus <span aria-hidden="true">*</span></label>
                            <textarea id="contact-address" name="address" rows="5" maxlength="5000" required data-character-count @error('address') aria-invalid="true" @enderror>{{ old('address', $contact->address) }}</textarea>
                            <small><span data-character-count-output>0</span> / 5.000 karakter</small>
                            @error('address')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field">
                            <label for="contact-email">Surel <span aria-hidden="true">*</span></label>
                            <div class="contact-input-wrap"><i class="fa-solid fa-envelope" aria-hidden="true"></i><input id="contact-email" name="email" type="email" maxlength="255" value="{{ old('email', $contact->email) }}" required @error('email') aria-invalid="true" @enderror></div>
                            @error('email')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field">
                            <label for="contact-phone">Nomor telepon <span aria-hidden="true">*</span></label>
                            <div class="contact-input-wrap"><i class="fa-solid fa-phone" aria-hidden="true"></i><input id="contact-phone" name="phone" type="tel" maxlength="30" value="{{ old('phone', $contact->phone) }}" placeholder="+62 821-4155-4377" required @error('phone') aria-invalid="true" @enderror></div>
                            @error('phone')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </section>

                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading">
                        <span aria-hidden="true"><i class="fa-solid fa-share-nodes"></i></span>
                        <div><p>Kanal Digital</p><h2>Media Sosial Resmi</h2></div>
                    </div>

                    <div class="contact-social-grid">
                        <div class="activity-field contact-social-field">
                            <label for="contact-instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i> Instagram</label>
                            <input id="contact-instagram" name="instagram" type="url" maxlength="2048" value="{{ old('instagram', $contact->instagram) }}" placeholder="https://instagram.com/nama-akun" @error('instagram') aria-invalid="true" @enderror>
                            @error('instagram')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field contact-social-field">
                            <label for="contact-youtube"><i class="fa-brands fa-youtube" aria-hidden="true"></i> YouTube</label>
                            <input id="contact-youtube" name="youtube" type="url" maxlength="2048" value="{{ old('youtube', $contact->youtube) }}" placeholder="https://youtube.com/@nama-kanal" @error('youtube') aria-invalid="true" @enderror>
                            @error('youtube')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field contact-social-field contact-social-facebook">
                            <label for="contact-facebook"><i class="fa-brands fa-facebook" aria-hidden="true"></i> Facebook</label>
                            <input id="contact-facebook" name="facebook" type="url" maxlength="2048" value="{{ old('facebook', $contact->facebook) }}" placeholder="https://facebook.com/nama-halaman" @error('facebook') aria-invalid="true" @enderror>
                            @error('facebook')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </section>

                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading">
                        <span aria-hidden="true"><i class="fa-solid fa-map-location-dot"></i></span>
                        <div><p>Lokasi Kampus</p><h2>Sematan Google Maps</h2></div>
                    </div>

                    <div class="activity-field">
                        <label for="contact-map-embed">URL atau kode iframe Google Maps</label>
                        <textarea id="contact-map-embed" name="map_embed" rows="7" maxlength="5000" placeholder="Tempel kode iframe dari menu Bagikan → Sematkan peta" @error('map_embed') aria-invalid="true" @enderror>{{ old('map_embed', $contact->map_embed) }}</textarea>
                        <small>Sistem hanya menyimpan URL HTTPS dari atribut src dan menolak iframe selain Google Maps.</small>
                        @error('map_embed')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                </section>
            </div>

            <aside class="contact-editor-aside">
                <section class="admin-panel contact-summary-card">
                    <p>Pratinjau Kontak</p>
                    <div class="contact-summary-item"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>{{ $contact->address ?: 'Alamat belum diatur' }}</span></div>
                    <div class="contact-summary-item"><i class="fa-solid fa-envelope" aria-hidden="true"></i><span>{{ $contact->email ?: 'Surel belum diatur' }}</span></div>
                    <div class="contact-summary-item"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>{{ $contact->phone ?: 'Telepon belum diatur' }}</span></div>
                </section>

                <section class="admin-panel contact-map-preview">
                    <p>Pratinjau Peta</p>
                    @if ($contact->map_embed)
                        <iframe src="{{ $contact->map_embed }}" title="Peta lokasi kampus" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    @else
                        <div class="contact-map-empty"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><strong>Lokasi Kampus</strong><span>Peta akan tampil setelah URL sematan Google Maps disimpan.</span></div>
                    @endif
                </section>

                <button class="admin-button admin-button-primary content-save-button" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan Kontak</button>
            </aside>
        </div>
    </form>
@endsection
