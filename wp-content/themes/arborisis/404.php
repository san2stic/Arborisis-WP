<?php
/**
 * 404 Error Page
 */

get_header();
?>

<div
    class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-secondary-50 dark:from-dark-900 dark:via-dark-900 dark:to-dark-800 flex items-center justify-center py-20">
    <div class="container-custom max-w-4xl text-center">

        <!-- 404 Illustration -->
        <div class="mb-12">
            <div
                class="text-9xl md:text-[200px] font-display font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent animate-fade-in">
                404
            </div>
        </div>

        <!-- Error Message -->
        <div class="mb-12 animate-slide-up">
            <h1 class="text-3xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Page Introuvable
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400 max-w-2xl mx-auto">
                Désolé, la page que vous recherchez semble avoir disparu dans les ondes sonores. Elle n'existe peut-être
                plus ou l'URL est incorrecte.
            </p>
        </div>

        <!-- Search -->
        <div class="max-w-2xl mx-auto mb-12 animate-slide-up" style="animation-delay: 0.2s;">
            <div class="relative">
                <input type="search" id="404-search"
                    class="w-full px-6 py-4 pr-12 rounded-xl border-2 border-dark-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-lg focus:ring-4 focus:ring-primary-500/50 focus:border-primary-500 transition-all"
                    placeholder="Rechercher un son...">
                <button
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-primary-600 hover:bg-primary-700 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-4 justify-center mb-12 animate-slide-up" style="animation-delay: 0.4s;">
            <a href="/" class="btn btn-primary btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Retour à l'Accueil
            </a>
            <a href="/explore" class="btn btn-outline btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Explorer les Sons
            </a>
            <a href="/map" class="btn btn-ghost btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Voir la Carte
            </a>
        </div>

        <!-- Popular Sounds -->
        <div class="animate-slide-up" style="animation-delay: 0.6s;">
            <h2 class="text-xl font-display font-bold text-dark-900 dark:text-dark-50 mb-6">
                Sons Populaires
            </h2>
            <div id="popular-sounds" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Populated by JS -->
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
                <div class="skeleton-card"></div>
            </div>
        </div>

        <!-- Fun Message -->
        <div class="mt-16 text-dark-500 dark:text-dark-500 animate-fade-in" style="animation-delay: 0.8s;">
            <p class="text-sm italic">
                "Dans le silence de l'erreur 404, on entend mieux les sons qui existent vraiment." - Proverbe
                d'Arborisis 🎧
            </p>
        </div>

    </div>
</div>

<script>
    // Load popular sounds
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const response = await fetch('/wp-json/arborisis/v1/sounds?orderby=popular&per_page=3');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            const sounds = data.sounds || []; // Access nested sounds array

            const container = document.getElementById('popular-sounds');

            if (sounds.length === 0) {
                container.innerHTML = '<p class="text-center text-dark-500">Aucun son disponible pour le moment.</p>';
                return;
            }

            container.innerHTML = sounds.map(sound => `
            <a href="/sound/${sound.id}" class="sound-card">
                <div class="sound-card-image">
                    <img src="${sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.jpg'}"
                         alt="${sound.title}"
                         loading="lazy">
                    <div class="sound-card-play-button">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-xl">
                            <svg class="w-8 h-8 text-primary-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="font-bold text-lg mb-2 line-clamp-1">${sound.title}</h3>
                    <div class="flex items-center justify-between text-xs text-dark-500">
                        <span>${window.formatDuration(sound.duration)}</span>
                        <span>${sound.plays_count || 0} plays</span>
                    </div>
                </div>
            </a>
        `).join('');
        } catch (error) {
            console.error('Failed to load popular sounds:', error);
        }

        // Search functionality
        const searchInput = document.getElementById('404-search');
        searchInput?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const query = e.target.value.trim();
                if (query) {
                    window.location.href = `/explore?search=${encodeURIComponent(query)}`;
                }
            }
        });
    });
</script>

<?php get_footer(); ?>