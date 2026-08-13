export function initChartThree() {
    const el = document.querySelector('#chartThree');
    if (!el) return;

    fetch(el.dataset.url)
        .then(r => r.json())
        .then(data => {
            new ApexCharts(el, {
                series: data.series,
                chart: { type: 'area', height: 350, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                colors: ['#1B365D', '#F79009'],
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05 },
                },
                dataLabels: { enabled: false },
                xaxis: { categories: data.labels, axisBorder: { show: false }, axisTicks: { show: false } },
                grid: { borderColor: '#E4E7EC', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: { theme: 'light' },
            }).render();
        });
}
