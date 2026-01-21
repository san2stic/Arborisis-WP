<?php
/**
 * Template Name: Graph
 * Description: Interactive graph exploration with D3.js
 */

get_header();
?>

<div class="graph-container">

    <!-- Graph SVG -->
    <svg id="graph-svg" class="absolute inset-0 w-full h-full"></svg>

    <!-- Controls Overlay -->
    <div class="absolute top-4 left-4 right-4 z-10 pointer-events-none">
        <div class="flex items-start justify-between gap-4">

            <!-- Left: Info & Search -->
            <div class="pointer-events-auto">
                <div class="glass dark:glass-dark rounded-xl p-4 shadow-xl max-w-md">
                    <h1 class="text-2xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                        Exploration Graphe
                    </h1>

                    <p class="text-sm text-dark-600 dark:text-dark-400 mb-4">
                        Découvrez des sons similaires à travers leurs tags, leur géolocalisation et leur popularité. Cliquez sur un son pour l'explorer.
                    </p>

                    <!-- Search seed -->
                    <div class="relative mb-4">
                        <input
                            type="search"
                            id="graph-search"
                            class="input pr-10"
                            placeholder="Rechercher un son pour commencer..."
                        >
                        <svg class="w-5 h-5 absolute right-3 top-1/2 -translate-y-1/2 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="hidden mb-4 max-h-60 overflow-y-auto custom-scrollbar bg-white dark:bg-dark-800 rounded-lg border border-dark-200 dark:border-dark-700">
                        <!-- Populated by JS -->
                    </div>

                    <!-- Quick Start -->
                    <div class="space-y-2">
                        <div class="text-xs uppercase tracking-wide text-dark-500 font-medium">Démarrage rapide</div>
                        <button class="w-full btn btn-ghost btn-sm justify-start" id="random-start">
                            🎲 Son aléatoire
                        </button>
                        <button class="w-full btn btn-ghost btn-sm justify-start" id="trending-start">
                            🔥 Son tendance
                        </button>
                    </div>

                    <!-- Current Stats -->
                    <div class="mt-4 pt-4 border-t border-white/20 dark:border-dark-700/50 space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-dark-600 dark:text-dark-400">Nœuds affichés:</span>
                            <span id="nodes-count" class="font-bold text-primary-600">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-dark-600 dark:text-dark-400">Connexions:</span>
                            <span id="edges-count" class="font-bold text-primary-600">0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-dark-600 dark:text-dark-400">Profondeur:</span>
                            <span id="depth-level" class="font-bold text-primary-600">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Controls & Legend -->
            <div class="pointer-events-auto">
                <div class="glass dark:glass-dark rounded-xl p-4 shadow-xl">

                    <!-- Graph Controls -->
                    <div class="space-y-3 mb-4">
                        <div>
                            <label class="text-xs uppercase tracking-wide text-dark-500 font-medium mb-2 block">
                                Profondeur d'exploration
                            </label>
                            <div class="flex gap-2">
                                <button class="btn btn-sm" data-depth="1">1</button>
                                <button class="btn btn-sm btn-primary" data-depth="2">2</button>
                                <button class="btn btn-sm" data-depth="3">3</button>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs uppercase tracking-wide text-dark-500 font-medium mb-2 block">
                                Nœuds maximum
                            </label>
                            <div class="flex gap-2">
                                <button class="btn btn-sm" data-max-nodes="25">25</button>
                                <button class="btn btn-sm btn-primary" data-max-nodes="50">50</button>
                                <button class="btn btn-sm" data-max-nodes="100">100</button>
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="pt-4 border-t border-white/20 dark:border-dark-700/50">
                        <div class="text-xs uppercase tracking-wide text-dark-500 font-medium mb-3">Légende</div>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-primary-500 rounded-full"></div>
                                <span class="text-dark-700 dark:text-dark-300">Son sélectionné</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-4 bg-secondary-500 rounded-full"></div>
                                <span class="text-dark-700 dark:text-dark-300">Son similaire</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-4 h-1 bg-dark-300 dark:bg-dark-600"></div>
                                <span class="text-dark-700 dark:text-dark-300">Connexion (similarité)</span>
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-dark-500">
                            💡 Taille du nœud = popularité<br>
                            💡 Épaisseur du lien = force de similarité
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-4 pt-4 border-t border-white/20 dark:border-dark-700/50 space-y-2">
                        <button id="center-graph" class="w-full btn btn-ghost btn-sm justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Centrer la vue
                        </button>
                        <button id="reset-graph" class="w-full btn btn-ghost btn-sm justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Réinitialiser
                        </button>
                        <button id="export-png" class="w-full btn btn-ghost btn-sm justify-start">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Exporter PNG
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Sound Details Panel (shown when node clicked) -->
    <div id="sound-panel" class="hidden absolute bottom-8 left-1/2 -translate-x-1/2 z-20 pointer-events-auto max-w-2xl w-full mx-4">
        <div class="card shadow-2xl">
            <div class="card-body">
                <button id="close-panel" class="absolute top-4 right-4 w-8 h-8 hover:bg-dark-100 dark:hover:bg-dark-700 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div id="panel-content" class="pr-8">
                    <!-- Populated by JS -->
                </div>

                <div class="flex gap-2 mt-4">
                    <button id="expand-node" class="btn btn-primary flex-1">
                        Étendre depuis ce son
                    </button>
                    <a id="view-sound" href="#" class="btn btn-outline flex-1">
                        Voir la page
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="graph-loading" class="absolute inset-0 bg-white dark:bg-dark-900 flex items-center justify-center z-30">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mx-auto mb-4"></div>
            <div class="text-lg font-medium text-dark-700 dark:text-dark-300">Initialisation du graphe...</div>
        </div>
    </div>

    <!-- Tooltip -->
    <div id="graph-tooltip" class="hidden absolute z-50 pointer-events-none">
        <div class="bg-dark-900 text-white px-3 py-2 rounded-lg text-sm shadow-xl max-w-xs">
            <div id="tooltip-content"></div>
        </div>
    </div>

</div>

<script>
// Graph configuration
window.graphConfig = {
    defaultDepth: 2,
    defaultMaxNodes: 50,
    apiEndpoint: '/wp-json/arborisis/v1/graph/explore',
    forceStrength: {
        charge: -300,
        link: 1,
        collide: 30,
    },
    nodeRadius: {
        min: 5,
        max: 20,
    },
    colors: {
        primary: '#16a34a',
        secondary: '#9333ea',
        edge: '#94a3b8',
    },
};

// UI Controls
document.addEventListener('DOMContentLoaded', () => {
    let currentDepth = 2;
    let currentMaxNodes = 50;

    // Depth buttons
    document.querySelectorAll('[data-depth]').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-depth]').forEach(btn => {
                btn.classList.remove('btn-primary');
            });
            button.classList.add('btn-primary');
            currentDepth = parseInt(button.dataset.depth);

            if (window.arbGraph && window.arbGraph.currentSeed) {
                window.arbGraph.explore(window.arbGraph.currentSeed, currentDepth, currentMaxNodes);
            }
        });
    });

    // Max nodes buttons
    document.querySelectorAll('[data-max-nodes]').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('[data-max-nodes]').forEach(btn => {
                btn.classList.remove('btn-primary');
            });
            button.classList.add('btn-primary');
            currentMaxNodes = parseInt(button.dataset.maxNodes);

            if (window.arbGraph && window.arbGraph.currentSeed) {
                window.arbGraph.explore(window.arbGraph.currentSeed, currentDepth, currentMaxNodes);
            }
        });
    });

    // Random start
    document.getElementById('random-start')?.addEventListener('click', async () => {
        if (window.arbGraph && window.arbGraph.randomStart) {
            await window.arbGraph.randomStart();
        }
    });

    // Trending start
    document.getElementById('trending-start')?.addEventListener('click', async () => {
        if (window.arbGraph && window.arbGraph.trendingStart) {
            await window.arbGraph.trendingStart();
        }
    });

    // Center graph
    document.getElementById('center-graph')?.addEventListener('click', () => {
        if (window.arbGraph && window.arbGraph.centerView) {
            window.arbGraph.centerView();
        }
    });

    // Reset graph
    document.getElementById('reset-graph')?.addEventListener('click', () => {
        if (window.arbGraph && window.arbGraph.reset) {
            window.arbGraph.reset();
        }
    });

    // Export PNG
    document.getElementById('export-png')?.addEventListener('click', () => {
        if (window.arbGraph && window.arbGraph.exportPNG) {
            window.arbGraph.exportPNG();
        }
    });

    // Close panel
    document.getElementById('close-panel')?.addEventListener('click', () => {
        document.getElementById('sound-panel')?.classList.add('hidden');
    });

    // Search
    let searchTimeout;
    const searchInput = document.getElementById('graph-search');
    const searchResults = document.getElementById('search-results');

    searchInput?.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value;

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`/wp-json/arborisis/v1/sounds?search=${encodeURIComponent(query)}&per_page=5`);
                const sounds = await response.json();

                if (sounds.length > 0) {
                    searchResults.innerHTML = sounds.map(sound => `
                        <button class="w-full text-left px-4 py-2 hover:bg-dark-100 dark:hover:bg-dark-700 transition-colors" data-sound-id="${sound.id}">
                            <div class="font-medium">${sound.title}</div>
                            <div class="text-xs text-dark-500">${sound.tags?.slice(0, 3).join(', ') || ''}</div>
                        </button>
                    `).join('');

                    searchResults.classList.remove('hidden');

                    // Add click listeners
                    searchResults.querySelectorAll('[data-sound-id]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const soundId = parseInt(btn.dataset.soundId);
                            if (window.arbGraph && window.arbGraph.explore) {
                                window.arbGraph.explore(soundId, currentDepth, currentMaxNodes);
                                searchResults.classList.add('hidden');
                                searchInput.value = '';
                            }
                        });
                    });
                } else {
                    searchResults.innerHTML = '<div class="px-4 py-2 text-sm text-dark-500">Aucun résultat</div>';
                    searchResults.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Search error:', error);
            }
        }, 300);
    });

    // Click outside to close search results
    document.addEventListener('click', (e) => {
        if (!searchInput?.contains(e.target) && !searchResults?.contains(e.target)) {
            searchResults?.classList.add('hidden');
        }
    });
});
</script>

<?php get_footer(); ?>
