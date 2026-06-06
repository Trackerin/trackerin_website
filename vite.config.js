import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        assetsInlineLimit: 0, // Prevent inlining small assets (like SVGs) so they exist in manifest.json
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
