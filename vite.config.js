import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

const env = loadEnv('all', process.cwd(), '');

export default defineConfig({
    base: '/build/',
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: ['resources/**', 'routes/**', 'app/**', 'config/**'],
        }),
        tailwindcss(),
        vue(),
    ],
    watch: {
        ignored: ['**/vendor/**'],
    },
    build: {
        chunkSizeWarningLimit: 1500,
        rollupOptions: {
            output: {
                manualChunks: {
                    vue: ['vue', 'vue-router', 'pinia'],
                    validation: ['@vuelidate/core', '@vuelidate/validators'],
                    utils: ['lodash', 'moment', 'dayjs', 'date-fns', 'xlsx'],
                },
            },
        },
        copyPublicDir: true,
    },
});
