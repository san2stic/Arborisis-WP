<?php
/**
 * Template Name: Map
 * Description: Interactive map with sound locations and clustering
 */

get_header();
?>

<div class="map-container">

    <!-- Map -->
    <div id="map" class="absolute inset-0"></div>

    <!-- Controls Overlay -->
    <div class="absolute top-4 left-4 right-4 z-10 pointer-events-none">
        <div class="flex items-start justify-between gap-4">

            <!-- Left: Search & Filters -->
            <div class="pointer-events-auto">
                <div class="glass dark:glass-dark rounded-xl p-4 shadow-xl max-w-md">
                    <h1 class="text-2xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                        Carte Interactive
                    </h1>

                    <!-- Search -->
                    <div class="relative mb-4">
                        <input type="search" id="map-search" class="input pr-10" placeholder="Rechercher un lieu...">
                        <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-dark-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Quick Filters -->
                    <div class="space-y-2">
                        <button class="w-full btn btn-ghost btn-sm justify-start" data-filter="all">
                            🌍 Tous les sons
                        </button>
                        <button class="w-full btn btn-ghost btn-sm justify-start" data-filter="nature">
                            🌿 Nature
                        </button>
                        <button class="w-full btn btn-ghost btn-sm justify-start" data-filter="urbain">
                            🏙️ Urbain
                        </button>
                        <button class="w-full btn btn-ghost btn-sm justify-start" data-filter="oiseaux">
                            🦜 Oiseaux
                        </button>
                        <button class="w-full btn btn-ghost btn-sm justify-start" data-filter="eau">
                            💧 Eau
                        </button>
                    </div>

                    <!-- Stats -->
                    <div class="mt-4 pt-4 border-t border-white/20 dark:border-dark-700/50">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-dark-600 dark:text-dark-400">Visible:</span>
                            <span id="visible-count" class="font-bold text-primary-600">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Legend & Actions -->
            <div class="pointer-events-auto">
                <div class="glass dark:glass-dark rounded-xl p-4 shadow-xl">

                    <!-- Map Style Toggle -->
                    <div class="flex gap-2 mb-4">
                        <button id="style-streets" class="btn btn-sm btn-primary">
                            Rues
                        </button>
                        <button id="style-satellite" class="btn btn-sm btn-ghost">
                            Satellite
                        </button>
                        <button id="style-terrain" class="btn btn-sm btn-ghost">
                            Terrain
                        </button>
                    </div>

                    <!-- Legend -->
                    <div class="space-y-2 text-sm">
                        <div class="font-bold text-dark-900 dark:text-dark-50 mb-2">Légende</div>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-primary-500 rounded-full shadow-glow"></div>
                            <span class="text-dark-700 dark:text-dark-300">Son unique</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 bg-secondary-500 rounded-full shadow-glow-lg flex items-center justify-center text-white text-xs font-bold">
                                5+</div>
                            <span class="text-dark-700 dark:text-dark-300">Cluster</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 pt-4 border-t border-white/20 dark:border-dark-700/50 space-y-2">
                        <button id="locate-me" class="w-full btn btn-ghost btn-sm justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Me localiser
                        </button>
                        <button id="fullscreen-toggle" class="w-full btn btn-ghost btn-sm justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                            Plein écran
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Sound Popup (shown when marker clicked) -->
    <div id="sound-popup"
        class="hidden absolute bottom-8 left-1/2 -translate-x-1/2 z-20 pointer-events-auto max-w-md w-full mx-4">
        <div class="card shadow-2xl">
            <div class="card-body">
                <button id="close-popup"
                    class="absolute top-4 right-4 w-8 h-8 hover:bg-dark-100 dark:hover:bg-dark-700 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div id="popup-content">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="map-loading" class="absolute inset-0 bg-white dark:bg-dark-900 flex items-center justify-center z-30">
        <div class="text-center">
            <div
                class="w-16 h-16 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-4">
            </div>
            <div class="text-lg font-medium text-dark-700 dark:text-dark-300">Chargement de la carte...</div>
        </div>
    </div>

</div>

<script>
    // Map will be initialized in map.js
    // This script provides the page-specific configuration
    window.mapConfig = {
        defaultCenter: [46.2276, 2.2137], // France center
        defaultZoom: 6,
        minZoom: 2,
        maxZoom: 18,
        clusterRadius: 60,
        apiEndpoint: '/wp-json/arborisis/v1/map/sounds',
    };

    // Fallback: Remove loading overlay if it takes too long
    setTimeout(() => {
        const loader = document.getElementById('map-loading');
        if (loader && !loader.classList.contains('hidden')) {
            console.warn('Map loading timed out, forcing removal of loader');
            loader.classList.add('hidden');

            // Show error message if map is empty
            const map = document.getElementById('map');
            if (map && !map.children.length) {
                map.innerHTML = `
                <div class="flex items-center justify-center w-full h-full text-dark-500">
                    <div class="text-center p-4">
                        <p class="font-bold mb-2">La carte n'a pas pu être chargée</p>
                        <p class="text-sm">Veuillez rafraîchir la page ou réessayer plus tard.</p>
                    </div>
                </div>
            `;
            }
        }
    }, 5000);

    // Filter functionality
    document.addEventListener('DOMContentLoaded', () => {
        const filterButtons = document.querySelectorAll('[data-filter]');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all
                filterButtons.forEach(btn => btn.classList.remove('btn-primary'));
                filterButtons.forEach(btn => btn.classList.add('btn-ghost'));

                // Add active to clicked
                button.classList.remove('btn-ghost');
                button.classList.add('btn-primary');

                // Trigger filter (handled by map.js)
                const filter = button.dataset.filter;
                if (window.arbMap && window.arbMap.setFilter) {
                    window.arbMap.setFilter(filter);
                }
            });
        });

        // Locate me button
        document.getElementById('locate-me')?.addEventListener('click', () => {
            if (window.arbMap && window.arbMap.locateUser) {
                window.arbMap.locateUser();
            }
        });

        // Fullscreen toggle
        document.getElementById('fullscreen-toggle')?.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });

        // Map style toggle
        const styleButtons = {
            streets: document.getElementById('style-streets'),
            satellite: document.getElementById('style-satellite'),
            terrain: document.getElementById('style-terrain'),
        };

        Object.entries(styleButtons).forEach(([style, button]) => {
            button?.addEventListener('click', () => {
                // Update button states
                Object.values(styleButtons).forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-ghost');
                });
                button.classList.remove('btn-ghost');
                button.classList.add('btn-primary');

                // Change map style (handled by map.js)
                if (window.arbMap && window.arbMap.setStyle) {
                    window.arbMap.setStyle(style);
                }
            });
        });

        // Close popup
        document.getElementById('close-popup')?.addEventListener('click', () => {
            document.getElementById('sound-popup')?.classList.add('hidden');
        });

        // Search location
        const searchInput = document.getElementById('map-search');
        let searchTimeout;

        searchInput?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value;

            if (query.length < 3) return;

            searchTimeout = setTimeout(async () => {
                try {
                    // Use Nominatim for geocoding
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                    const results = await response.json();

                    if (results.length > 0 && window.arbMap) {
                        const { lat, lon } = results[0];
                        window.arbMap.flyTo([parseFloat(lat), parseFloat(lon)], 12);
                    }
                } catch (error) {
                    console.error('Geocoding error:', error);
                }
            }, 500);
        });
    });
</script>

<?php get_footer(); ?>