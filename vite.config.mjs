import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const feSrc = path.resolve(__dirname, 'resources/front-end/src');

export default defineConfig({
  plugins: [
    tailwindcss(),
    laravel({
      input: 'resources/front-end/src/main.js',
      refresh: true,
    }),
    vue(),
  ],

  resolve: {
    extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue'],
    alias: {
      components: path.resolve(feSrc, 'components'),
      layouts: path.resolve(feSrc, 'layouts'),
      pages: path.resolve(feSrc, 'pages'),
      '@': feSrc,
    },
  },

  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern',
      },
    },
  },

  test: {
    globals: true,
    environment: 'happy-dom',
    include: ['resources/front-end/**/*.{spec,test}.{js,ts}'],
    resolve: {
      alias: {
        '@': feSrc,
      },
    },
  },
});
