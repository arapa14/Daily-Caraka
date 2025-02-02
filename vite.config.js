import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  server: {
    host: true, // agar Vite mendengarkan pada semua IP
    hmr: {
      // Pastikan host untuk HMR sesuai dengan URL ngrok
      host: 'localhost'
    }
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
});
