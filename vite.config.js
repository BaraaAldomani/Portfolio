import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/v3.css',
                'resources/js/v3.js',
            ],
            refresh: true,
            fonts: [
                bunny('Cairo', {
                    weights: [400, 600, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    build: {
        // Include Firefox (which has no -webkit-backdrop-filter) so the CSS
        // pipeline must emit the standard backdrop-filter, and Safari 15 so it
        // still emits the -webkit- prefix. Keeps both in the built bundle.
        cssTarget: ['chrome111', 'firefox121', 'safari15', 'edge111'],
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
