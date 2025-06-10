import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// export default defineConfig({
export default defineConfig(({ mode }) => {
    return {
        esbuild: {
            pure: mode === 'production' ? ['console.log'] : []
          },
        plugins: [
            laravel({
                input: ['resources/js/app.js', "resources/js/main.js"],
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
    }
});
