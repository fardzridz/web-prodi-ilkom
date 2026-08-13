export function initChartTwo() {
    const el = document.querySelector('#chartTwo');
    if (!el) return;

    fetch(el.dataset.url)
        .then(r => r.json())
        .then(data => {
            new ApexCharts(el, {
                series: data.series,
                chart: { type: 'donut', height: 310, fontFamily: 'Outfit, sans-serif' },
                labels: data.labels,
                colors: ['#1B365D', '#12B76A', '#F79009', '#F04438'],
                legend: { position: 'bottom', fontSize: '14px', markers: { size: 8, strokeWidth: 0 } },
                plotOptions: { pie: { donut: { size: '60%' } } },
                dataLabels: {
                    enabled: true,
                    style: { fontSize: '14px', fontWeight: 600 },
                    dropShadow: { enabled: false },
                },
                tooltip: { theme: 'light' },
            }).render();
        });
}
