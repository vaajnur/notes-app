import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    cacheDir: '/tmp/notes-vite-cache',
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/swagger.js',
            ],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        host: '0.0.0.0',
        origin: process.env.VITE_DEV_SERVER_URL ?? 'http://localhost:5173',
        hmr: {
            clientPort: Number(process.env.VITE_HMR_PORT ?? 5173),
            path: '/vite-hmr',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
