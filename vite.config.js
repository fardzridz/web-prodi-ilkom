import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';

export default defineConfig({
    server: {
        host: 'localhost',
        port: 5173,
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/quill-init.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        ViteImageOptimizer({
            png: { quality: 80 },
            jpeg: { quality: 80 },
            jpg: { quality: 80 },
            webp: { quality: 80 },
            avif: { quality: 70 },
        }),
    ],
    build: {
        sourcemap: false,
        cssCodeSplit: true,
        assetsInlineLimit: 4096,
        chunkSizeWarningLimit: 500,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['alpinejs'],
                    charts: ['apexcharts'],
                },
            },
        },
    },
});
