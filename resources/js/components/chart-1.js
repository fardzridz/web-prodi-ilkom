export function initChartOne() {
    const el = document.querySelector('#chartOne');
    if (!el) return;

    fetch(el.dataset.url)
        .then(r => r.json())
        .then(data => {
            new ApexCharts(el, {
                series: [{ name: 'Kegiatan', data: data.counts }],
                chart: { type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                colors: ['#1B365D'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: data.labels, axisBorder: { show: false }, axisTicks: { show: false } },
                grid: { borderColor: '#E4E7EC', strokeDashArray: 4 },
                tooltip: {
                    theme: 'light',
                    y: { formatter: (val) => val + ' kegiatan' },
                },
            }).render();
        });
}
