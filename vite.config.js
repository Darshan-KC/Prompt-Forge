import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600, 700],
                }),
                bunny('JetBrains Mono', {
                    weights: [400, 500, 600, 700],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // Docker: the container binds 0.0.0.0 but browsers must reach Vite
        // through the published host port (see VITE_ORIGIN in compose).
        origin: process.env.VITE_ORIGIN ?? 'http://localhost:5173',
        // laravel-vite-plugin derives its CORS allow-list from server.origin,
        // so a fixed origin locks dev assets to a single host and blocks the
        // Laravel app (served from APP_URL, a different origin) from loading
        // them. Reflect the requesting origin instead — dev only, matching
        // Vite's default cross-origin behavior.
        cors: { origin: true },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});