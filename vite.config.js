import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/all.min.css',
                'resources/css/layouts/common.css',
                'resources/css/layouts/header.css',
                'resources/css/layouts/sidebar.css',
                'resources/css/home/index.css',
                'resources/css/bill/index.css',
                'resources/css/bill/create.css',
                'resources/css/line/checkin.css',
                'resources/css/mix/mix.css',
                'resources/css/situation.css',
                'resources/js/common.js',
                'resources/js/home.js',
                'resources/js/user.js',
                'resources/js/bill.js',
                'resources/js/mix.js',
                'resources/js/situation.js'
            ],
            refresh: true,
        }),
    ],
});
