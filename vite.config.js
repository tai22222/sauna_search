import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: 'http://127.0.0.1:5173/',
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        watch: {
            // 3秒間の更新がない場合にまとめて更新する
            // (ミリ秒単位で指定)
            interval: 3000,
        },
    },
});



// vendor/inertiajs/inertia-laravel/src/Response.phpの一部ですが
// .$request->getBaseUri(),部分が不要で修正したので注意
// URLの重複表記を解決するため

// $page = [
//   'component' => $this->component,
//   'props' => $props,
//   'url' => $request->getBaseUrl().$request->getRequestUri(),
//   'version' => $this->version,
// ];