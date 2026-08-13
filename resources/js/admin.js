import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;

Alpine.store('sidebar', {
    isExpanded: false,
    isMobileOpen: false,
    isHovered: false,

    toggle() {
        this.isExpanded = !this.isExpanded;
    },

    toggleMobile() {
        this.isMobileOpen = !this.isMobileOpen;
    },

    closeMobile() {
        this.isMobileOpen = false;
    },
});

Alpine.store('theme', {
    isDark: localStorage.getItem('theme') === 'dark',

    init() {
        if (this.isDark || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            this.isDark = true;
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    toggle() {
        this.isDark = !this.isDark;
        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
        document.documentElement.classList.toggle('dark', this.isDark);
    },
});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('#chartOne')) {
        import('./components/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart-3').then(module => module.initChartThree());
    }
});
