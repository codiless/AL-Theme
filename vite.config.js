import { defineConfig } from 'vite';
import { resolve } from 'node:path';
export default defineConfig({
  build: {
    outDir: 'assets/dist', emptyOutDir: true, manifest: 'manifest.json',
    cssCodeSplit: true, minify: true, target: 'es2020',
    rollupOptions: {
      input: {
        app: resolve('assets/src/css/app.css'),
        listing: resolve('assets/src/css/listing.css'),
        'editor-style': resolve('assets/src/css/editor.css'),
        editor: resolve('assets/src/js/editor.js'),
        'load-more': resolve('assets/src/js/load-more.js'),
        navigation: resolve('assets/src/js/navigation.js'),
      },
      output: { entryFileNames: '[name]-[hash].js', assetFileNames: '[name]-[hash][extname]' },
    },
  },
});
