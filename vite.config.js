import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite'; // 1. Import dòng này

export default defineConfig({
    plugins: [
        tailwindcss(), // 2. Thêm hàm này vào đầu mảng plugins
        laravel({
            input: ['resources/ts/index.css', 'resources/ts/main.ts'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    // ... phần alias '@' giữ nguyên
});