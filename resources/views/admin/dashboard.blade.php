@extends('layouts.admin')

@section('title', 'Dashboard Pengelola | Program Studi Ilmu Komputer')
@section('page-section', 'Dashboard')
@section('page-heading', 'Dashboard')
@section('page-helper', 'Ringkasan pekerjaan konten dan kesiapan halaman situs prodi.')

@section('content')
    <section class="admin-grid">
        @foreach ($summaryCards as $card)
            <article class="stat-card">
                <div class="stat-card-heading">
                    <span>{{ $card['label'] }}</span>
                    <i class="fa-solid {{ $card['icon'] }}" aria-hidden="true"></i>
                </div>
                <strong>{{ number_format($card['count'], 0, ',', '.') }}</strong>
                <small>{{ $card['detail'] }}</small>
            </article>
        @endforeach
    </section>

    <section class="admin-status-grid" aria-label="Ringkasan status konten">
        @foreach ($statusCards as $card)
            <article class="admin-status-card admin-status-card-{{ $card['tone'] }}">
                <span>{{ $card['label'] }}</span>
                <strong>{{ number_format($card['count'], 0, ',', '.') }}</strong>
            </article>
        @endforeach
    </section>

    <section class="admin-dashboard-layout">
        <div class="admin-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Aktivitas Terbaru</h2>
                </div>
                <span class="admin-status-badge">{{ $latestContent->count() }} terbaru</span>
            </div>

            @if ($latestContent->isEmpty())
                <x-admin.empty-state
                    title="Belum ada aktivitas konten"
                    message="Data kegiatan, dosen, dokumen, dan alumni terbaru akan muncul di sini."
                    icon="fa-clock-rotate-left"
                />
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">Konten</th>
                                <th scope="col">Jenis</th>
                                <th scope="col">Status</th>
                                <th scope="col">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestContent as $item)
                                <tr>
                                    <td><strong>{{ $item['title'] }}</strong></td>
                                    <td>{{ $item['type'] }}</td>
                                    <td>
                                        <span class="admin-content-badge admin-content-badge-{{ $item['tone'] }}">
                                            {{ $item['status_label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <time datetime="{{ $item['updated_at']->toIso8601String() }}">
                                            {{ $item['updated_at']->locale('id')->diffForHumans() }}
                                        </time>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <aside class="admin-dashboard-aside">
            <section class="admin-panel admin-quick-actions">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Aksi Cepat</h2>
                    </div>
                </div>
                <div class="admin-action-list">
                    <a class="admin-button admin-button-primary" href="{{ route('admin.kegiatan.create') }}">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        Tambah Kegiatan
                    </a>
                    <a class="admin-button admin-button-secondary" href="{{ route('admin.dosen.create') }}">Tambah Dosen</a>
                    <a class="admin-button admin-button-secondary" href="{{ route('admin.dokumen.create') }}">Unggah Dokumen</a>
                </div>
            </section>

            <section class="admin-panel admin-readiness">
                <div class="admin-panel-heading">
                    <div>
                        <h2>Kesiapan Halaman Publik</h2>
                    </div>
                </div>
                <ul>
                    @foreach ($publicReadiness as $item)
                        <li>
                            <span>{{ $item['label'] }}</span>
                            <strong class="admin-readiness-{{ $item['tone'] }}">{{ $item['status'] }}</strong>
                        </li>
                    @endforeach
                </ul>
            </section>
        </aside>
    </section>
@endsection
