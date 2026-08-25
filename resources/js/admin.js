import Alpine from 'alpinejs';

window.Alpine = Alpine;

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

const needsApexCharts = () =>
    document.querySelector('#chartOne') ||
    document.querySelector('#chartTwo') ||
    document.querySelector('#chartThree');

if (needsApexCharts()) {
    import('apexcharts').then(({ default: ApexCharts }) => {
        window.ApexCharts = ApexCharts;
        window.dispatchEvent(new CustomEvent('apexcharts:ready'));
    });
}
