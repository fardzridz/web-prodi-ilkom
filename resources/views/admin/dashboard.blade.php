@extends('layouts.admin')

@section('title', 'Dashboard Pengelola | Program Studi Ilmu Komputer')
@section('page-heading', 'Dashboard')
@section('page-helper', 'Ringkasan pekerjaan konten dan kesiapan halaman situs prodi.')

@section('content')
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4 mb-6">
        @foreach ($summaryCards as $card)
            @php
                $svgIcons = [
                    'fa-calendar-days' => '<path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5v-5z" fill="currentColor"/>',
                    'fa-chalkboard-user' => '<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>',
                    'fa-file-lines' => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="currentColor"/>',
                    'fa-user-graduate' => '<path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z" fill="currentColor"/>',
                    'fa-clock-rotate-left' => '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" fill="currentColor"/>',
                ];
                $svgIcon = $svgIcons[$card['icon']] ?? '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" fill="currentColor"/>';
            @endphp
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $card['label'] }}</span>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">{!! $svgIcon !!}</svg>
                    </span>
                </div>
                <p class="text-3xl font-semibold text-gray-800 dark:text-white/90">{{ number_format($card['count'], 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $card['detail'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4 mb-6">
        @foreach ($statusCards as $card)
            @php
                $colors = ['draft' => 'warning', 'scheduled' => 'info', 'published' => 'success', 'active' => 'primary'];
                $svgStatusIcons = [
                    'draft' => '<path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>',
                    'scheduled' => '<path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>',
                    'published' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>',
                    'active' => '<path d="M11 21h-1l1-7H7.5c-.58 0-.57-.32-.38-.66.19-.34.05-.08.07-.12C8.48 10.94 10.42 7.54 13 3h1l-1 7h3.5c.49 0 .56.33.47.51l-.07.15C12.96 17.55 11 21 11 21z"/>',
                ];
                $color = $colors[$card['tone']] ?? 'primary';
                $svgIcon = $svgStatusIcons[$card['tone']] ?? '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 5h2v6h-2zm0 8h2v2h-2z"/>';
            @endphp
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ number_format($card['count'], 0, ',', '.') }}</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-{{ $color }}-50 text-{{ $color }}-500 dark:bg-{{ $color }}-500/15">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">{!! $svgIcon !!}</svg>
                    </span>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-3 mb-6">
        <x-admin.component-card title="Kegiatan 6 Bulan Terakhir" desc="Jumlah kegiatan baru per bulan.">
            <div id="chartOne" data-url="{{ route('admin.dashboard') }}" data-labels='@json($chartActivityMonthly['labels'])' data-counts='@json($chartActivityMonthly['counts'])' class="min-h-[280px] sm:min-h-[310px] overflow-x-auto"></div>
        </x-admin.component-card>

        <x-admin.component-card title="Distribusi Data" desc="Prosentase per kategori data.">
            <div id="chartTwo" data-url="{{ route('admin.dashboard') }}" data-series='@json($chartStatusDistribution['series'])' data-labels='@json($chartStatusDistribution['labels'])' class="min-h-[280px] sm:min-h-[310px]"></div>
        </x-admin.component-card>

        <x-admin.component-card title="Aktivitas & Alumni" desc="Tren kegiatan dan alumni bulanan.">
            <div id="chartThree" data-url="{{ route('admin.dashboard') }}" data-series='@json($chartCombinedMonthly['series'])' data-labels='@json($chartCombinedMonthly['labels'])' class="min-h-[280px] sm:min-h-[310px] overflow-x-auto"></div>
        </x-admin.component-card>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-admin.component-card title="Aktivitas Terbaru">
                @if ($latestContent->isEmpty())
                    <div class="py-12 text-center">
                        <div class="mb-3 flex justify-center text-gray-400 dark:text-gray-500">
                            <svg class="fill-current" width="40" height="40" viewBox="0 0 24 24" fill="none">
                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" fill=""/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada aktivitas konten</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Data akan muncul setelah Anda mulai mengelola konten.</p>
                    </div>
                @else
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Konten</th>
                                    <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Jenis</th>
                                    <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="py-3 font-medium text-gray-500 dark:text-gray-400">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($latestContent as $item)
                                    @php
                                        $badgeColors = [
                                            'draft' => 'warning',
                                            'scheduled' => 'info',
                                            'published' => 'success',
                                            'active' => 'primary',
                                            'inactive' => 'light',
                                        ];
                                        $badgeColor = $badgeColors[$item['tone']] ?? 'light';
                                    @endphp
                                    <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                        <td class="py-3 pr-4 font-medium text-gray-800 dark:text-white/90 whitespace-nowrap">{{ \Illuminate\Support\Str::limit($item['title'], 40) }}</td>
                                        <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $item['type'] }}</td>
                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            <x-admin.badge :color="$badgeColor" size="sm">{{ $item['status_label'] }}</x-admin.badge>
                                        </td>
                                        <td class="py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            @php
                                                $rawUpdatedAt = $item['updated_at'];
                                                $updatedAt = $rawUpdatedAt instanceof \Carbon\CarbonInterface ? $rawUpdatedAt : \Carbon\Carbon::parse((string) $rawUpdatedAt);
                                            @endphp
                                            <time datetime="{{ $updatedAt->toIso8601String() }}">
                                                {{ $updatedAt->locale('id')->diffForHumans() }}
                                            </time>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-admin.component-card>
        </div>

        <div class="space-y-6">
            <x-admin.component-card title="Aksi Cepat">
                <div class="flex flex-col gap-3">
                    <a href="{{ route('admin.kegiatan.create') }}"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3.5 text-sm font-medium text-white transition">
                        <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill=""/>
                        </svg>
                        Tambah Kegiatan
                    </a>
                    <a href="{{ route('admin.dosen.create') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                        Tambah Dosen
                    </a>
                    <a href="{{ route('admin.dokumen.create') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                        Unggah Dokumen
                    </a>
                </div>
            </x-admin.component-card>

            <x-admin.component-card title="Kesiapan Halaman Publik">
                <ul class="-my-2 divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($publicReadiness as $item)
                        <li class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $item['label'] }}</span>
                            @if ($item['tone'] === 'ready')
                                <x-admin.badge color="success" size="sm">{{ $item['status'] }}</x-admin.badge>
                            @else
                                <x-admin.badge color="warning" size="sm">{{ $item['status'] }}</x-admin.badge>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </x-admin.component-card>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const initCharts = () => {
        const chartOneEl = document.querySelector('#chartOne');
        if (chartOneEl) {
            const chartOneLabels = JSON.parse(chartOneEl.dataset.labels);
            const chartOneCounts = JSON.parse(chartOneEl.dataset.counts);
            new ApexCharts(chartOneEl, {
                series: [{ name: 'Kegiatan', data: chartOneCounts }],
                chart: { type: 'bar', height: 310, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                colors: ['#1B365D'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '38%', borderRadiusApplication: 'end' } },
                dataLabels: { enabled: false },
                xaxis: { categories: chartOneLabels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '12px', colors: '#667085' } } },
                yaxis: { labels: { style: { fontSize: '12px', colors: '#667085' } } },
                grid: { borderColor: '#E4E7EC', strokeDashArray: 4 },
                tooltip: { theme: 'light', y: { formatter: (val) => val + ' kegiatan' } },
                responsive: [{ breakpoint: 640, options: { chart: { height: 260 }, plotOptions: { bar: { columnWidth: '52%' } }, xaxis: { labels: { rotate: -30, style: { fontSize: '11px' } } } } }],
            }).render();
        }

        const chartTwoEl = document.querySelector('#chartTwo');
        if (chartTwoEl) {
            const chartTwoSeries = JSON.parse(chartTwoEl.dataset.series);
            const chartTwoLabels = JSON.parse(chartTwoEl.dataset.labels);
            new ApexCharts(chartTwoEl, {
                series: chartTwoSeries,
                chart: { type: 'donut', height: 310, fontFamily: 'Outfit, sans-serif', foreColor: '#FFFFFF', background: 'transparent' },
                labels: chartTwoLabels,
                colors: ['#1B365D', '#12B76A', '#F59E0B', '#0BA5EC'],
                legend: { position: 'bottom', fontSize: '13px', labels: { colors: '#344054' }, markers: { width: 10, height: 10, radius: 12 }, itemMargin: { horizontal: 10, vertical: 4 } },
                plotOptions: { pie: { donut: { size: '62%', labels: { show: false } }, expandOnClick: false, dataLabels: { offset: 0, minAngleToShowLabel: 0 } } },
                stroke: { width: 2, colors: ['#fff'] },
                dataLabels: { enabled: true, formatter: (val) => Math.round(val) + '%', style: { fontSize: '11px', fontWeight: 700, colors: ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'] }, background: { enabled: false }, dropShadow: { enabled: false } },
                theme: { mode: 'dark' },
                tooltip: { theme: 'dark', y: { formatter: (val) => val + ' data' } },
                responsive: [{ breakpoint: 640, options: { chart: { height: 280 }, legend: { fontSize: '12px', position: 'bottom' }, plotOptions: { pie: { donut: { size: '58%' } } }, dataLabels: { style: { fontSize: '10px' } } } }],
            }).render();
        }

        const chartThreeEl = document.querySelector('#chartThree');
        if (chartThreeEl) {
            const chartThreeSeries = JSON.parse(chartThreeEl.dataset.series);
            const chartThreeLabels = JSON.parse(chartThreeEl.dataset.labels);
            new ApexCharts(chartThreeEl, {
                series: chartThreeSeries,
                chart: { type: 'area', height: 310, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif', zoom: { enabled: false } },
                colors: ['#1B365D', '#F59E0B'],
                stroke: { curve: 'smooth', width: 2.5 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.32, opacityTo: 0.04, stops: [0, 90, 100] } },
                dataLabels: { enabled: false },
                markers: { size: 0, hover: { size: 5 } },
                xaxis: { categories: chartThreeLabels, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { fontSize: '12px', colors: '#667085' }, rotate: 0 } },
                yaxis: { labels: { style: { fontSize: '12px', colors: '#667085' } } },
                grid: { borderColor: '#E4E7EC', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right', fontSize: '13px', labels: { colors: '#344054' }, markers: { width: 10, height: 10, radius: 12 } },
                tooltip: { theme: 'light', shared: true, intersect: false },
                responsive: [{ breakpoint: 640, options: { chart: { height: 260 }, legend: { position: 'bottom', horizontalAlign: 'center', fontSize: '12px' }, xaxis: { labels: { rotate: -30, style: { fontSize: '11px' } } }, stroke: { width: 2 } } }],
            }).render();
        }
    };

    if (window.ApexCharts) {
        initCharts();
    } else {
        window.addEventListener('apexcharts:ready', initCharts, { once: true });
    }
</script>
@endpush
