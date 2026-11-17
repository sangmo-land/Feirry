import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import path from 'node:path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(import.meta.dirname, 'resources/js'),
        },
    },
    build: {
        reportCompressedSize: false,
        chunkSizeWarningLimit: 700,
        rollupOptions: {
            output: {
                // Split the libraries out of the app bundle. They change far
                // less often than site content, so returning visitors on
                // mobile data keep them cached across deploys.
                manualChunks(id) {
                    if (!id.includes('node_modules')) return undefined;

                    const vendor = (names) =>
                        new RegExp(`[\\\\/]node_modules[\\\\/](${names})[\\\\/]`).test(id);

                    if (vendor('react|react-dom|scheduler|@inertiajs')) return 'react';
                    // motion / gsap / lenis are only used by the legacy careers
                    // pages now: left to automatic splitting so the V7 pages
                    // never download them.

                    return undefined;
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**', '**/vendor/**'],
        },
    },
});
