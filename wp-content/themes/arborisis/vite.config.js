import { defineConfig } from 'vite';
import { resolve } from 'path';
import viteCompression from 'vite-plugin-compression';

export default defineConfig({
  base: '/wp-content/themes/arborisis/dist/',

  build: {
    outDir: 'dist',
    manifest: true,
    sourcemap: false,
    minify: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/main.js'),
        map: resolve(__dirname, 'src/map.js'),
        graph: resolve(__dirname, 'src/graph.js'),
        player: resolve(__dirname, 'src/player.js'),
        particles: resolve(__dirname, 'src/js/particles.js'),
        animations: resolve(__dirname, 'src/js/animations.js'),
        keyboard: resolve(__dirname, 'src/js/keyboard-shortcuts.js'),
      },
      output: {
        entryFileNames: '[name].[hash].js',
        chunkFileNames: '[name].[hash].js',
        assetFileNames: '[name].[hash].[ext]',
        manualChunks: {
          vendor: ['leaflet', 'd3', 'wavesurfer.js'],
        },
      },
    },
    chunkSizeWarningLimit: 1000,
  },

  plugins: [
    viteCompression({
      algorithm: 'gzip',
      ext: '.gz',
    }),
  ],

  server: {
    port: 3000,
    strictPort: true,
    hmr: {
      host: 'localhost',
      protocol: 'ws',
    },
  },
});
