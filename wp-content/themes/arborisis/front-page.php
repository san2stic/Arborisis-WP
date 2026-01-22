<?php
/**
 * Template Name: Front Page
 * Description: Homepage with hero, featured sounds, and stats
 */

get_header();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title animate-fade-in">
            Explorez les Paysages Sonores du Monde
        </h1>
        <p class="hero-subtitle animate-slide-up">
            Une plateforme collaborative de field recording. Découvrez, partagez et explorez des milliers d'enregistrements sonores géolocalisés.
        </p>

        <!-- Hero Search -->
        <div class="mt-12 max-w-2xl mx-auto animate-slide-up" style="animation-delay: 0.2s;">
            <div class="relative">
                <input
                    type="search"
                    id="hero-search"
                    class="w-full px-6 py-4 pr-12 rounded-xl border-2 border-dark-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-lg focus:ring-4 focus:ring-primary-500/50 focus:border-primary-500 transition-all"
                    placeholder="Rechercher des sons, tags, lieux... (ex: oiseaux, forêt, paris)"
                >
                <button class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-primary-600 hover:bg-primary-700 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>

            <!-- Quick Filters -->
            <div class="flex flex-wrap gap-2 mt-4 justify-center">
                <a href="/explore?tag=nature" class="badge badge-primary cursor-pointer hover:scale-105 transition-transform">🌿 Nature</a>
                <a href="/explore?tag=oiseaux" class="badge badge-primary cursor-pointer hover:scale-105 transition-transform">🦜 Oiseaux</a>
                <a href="/explore?tag=eau" class="badge badge-primary cursor-pointer hover:scale-105 transition-transform">💧 Eau</a>
                <a href="/explore?tag=urbain" class="badge badge-primary cursor-pointer hover:scale-105 transition-transform">🏙️ Urbain</a>
                <a href="/explore?tag=ambiance" class="badge badge-primary cursor-pointer hover:scale-105 transition-transform">🎧 Ambiance</a>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap gap-4 justify-center mt-8 animate-slide-up" style="animation-delay: 0.4s;">
            <a href="/map" class="btn btn-primary btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Explorer la Carte
            </a>
            <a href="/graph" class="btn btn-outline btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Découvrir le Graphe
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-dark-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

<!-- Live Stats Bar -->
<section class="py-16 bg-gradient-to-r from-primary-50 via-white to-secondary-50 dark:from-dark-900 dark:via-dark-900 dark:to-dark-800 border-y border-dark-200 dark:border-dark-700">
    <div class="container-custom">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8" id="live-stats">
            <div class="stat">
                <div class="stat-value" data-stat="sounds">-</div>
                <div class="stat-label">Enregistrements</div>
            </div>
            <div class="stat">
                <div class="stat-value" data-stat="plays">-</div>
                <div class="stat-label">Écoutes</div>
            </div>
            <div class="stat">
                <div class="stat-value" data-stat="users">-</div>
                <div class="stat-label">Contributeurs</div>
            </div>
            <div class="stat">
                <div class="stat-value" data-stat="countries">-</div>
                <div class="stat-label">Pays</div>
            </div>
        </div>
    </div>
</section>

<!-- Trending Sounds -->
<section class="py-20 bg-white dark:bg-dark-900">
    <div class="container-custom">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-dark-900 dark:text-dark-50">
                    🔥 Sons Tendance
                </h2>
                <p class="text-dark-600 dark:text-dark-400 mt-2">
                    Les enregistrements les plus populaires cette semaine
                </p>
            </div>
            <a href="/explore?orderby=trending" class="btn btn-ghost">
                Voir tout
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div id="trending-sounds" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Populated by JS -->
            <?php for ($i = 0; $i < 8; $i++) : ?>
                <div class="skeleton-card"></div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- Recent Sounds -->
<section class="py-20 bg-dark-50 dark:bg-dark-950">
    <div class="container-custom">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-dark-900 dark:text-dark-50">
                    🆕 Derniers Enregistrements
                </h2>
                <p class="text-dark-600 dark:text-dark-400 mt-2">
                    Découvrez les nouveaux sons ajoutés par la communauté
                </p>
            </div>
            <a href="/explore?orderby=recent" class="btn btn-ghost">
                Voir tout
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div id="recent-sounds" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Populated by JS -->
            <?php for ($i = 0; $i < 8; $i++) : ?>
                <div class="skeleton-card"></div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-20 bg-white dark:bg-dark-900">
    <div class="container-custom">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-display font-bold text-dark-900 dark:text-dark-50">
                Explorez de Nouvelles Façons
            </h2>
            <p class="text-dark-600 dark:text-dark-400 mt-4 max-w-2xl mx-auto">
                Découvrez notre plateforme à travers différentes interfaces interactives
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Map Feature -->
            <a href="/map" class="card group cursor-pointer">
                <div class="aspect-video bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center relative overflow-hidden">
                    <svg class="w-20 h-20 text-white/80 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                </div>
                <div class="card-body">
                    <h3 class="text-xl font-display font-bold mb-2">Carte Interactive</h3>
                    <p class="text-dark-600 dark:text-dark-400 text-sm">
                        Explorez les enregistrements géolocalisés sur une carte mondiale avec clustering intelligent
                    </p>
                </div>
            </a>

            <!-- Graph Feature -->
            <a href="/graph" class="card group cursor-pointer">
                <div class="aspect-video bg-gradient-to-br from-secondary-500 to-secondary-600 flex items-center justify-center relative overflow-hidden">
                    <svg class="w-20 h-20 text-white/80 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                </div>
                <div class="card-body">
                    <h3 class="text-xl font-display font-bold mb-2">Graphe d'Exploration</h3>
                    <p class="text-dark-600 dark:text-dark-400 text-sm">
                        Découvrez des sons similaires à travers une visualisation interactive basée sur les tags et la géolocalisation
                    </p>
                </div>
            </a>

            <!-- Stats Feature -->
            <a href="/stats" class="card group cursor-pointer">
                <div class="aspect-video bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center relative overflow-hidden">
                    <svg class="w-20 h-20 text-white/80 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                </div>
                <div class="card-body">
                    <h3 class="text-xl font-display font-bold mb-2">Statistiques</h3>
                    <p class="text-dark-600 dark:text-dark-400 text-sm">
                        Analysez les tendances, les leaderboards et les données d'utilisation de la plateforme
                    </p>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<?php if (!is_user_logged_in()) : ?>
<section class="py-20 bg-gradient-to-br from-primary-600 to-secondary-600">
    <div class="container-custom text-center text-white">
        <h2 class="text-3xl md:text-5xl font-display font-bold mb-6">
            Rejoignez la Communauté
        </h2>
        <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
            Partagez vos field recordings, explorez les sons du monde entier, et contribuez à cette bibliothèque sonore collaborative
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="<?php echo wp_registration_url(); ?>" class="btn bg-white text-primary-600 hover:bg-dark-50 btn-lg">
                Créer un Compte
            </a>
            <a href="/about" class="btn bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white border-2 border-white/30 btn-lg">
                En Savoir Plus
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    // Load live stats
    try {
        const statsRes = await fetch('/wp-json/arborisis/v1/stats/global');
        const stats = await statsRes.json();

        document.querySelector('#live-stats [data-stat="sounds"]').textContent = stats.total_sounds?.toLocaleString() || '0';
        document.querySelector('#live-stats [data-stat="plays"]').textContent = stats.total_plays?.toLocaleString() || '0';
        document.querySelector('#live-stats [data-stat="users"]').textContent = stats.total_users?.toLocaleString() || '0';
        document.querySelector('#live-stats [data-stat="countries"]').textContent = stats.countries_count?.toLocaleString() || '0';
    } catch (error) {
        console.error('Failed to load stats:', error);
    }

    // Load trending sounds
    try {
        const trendingRes = await fetch('/wp-json/arborisis/v1/sounds?orderby=trending&per_page=8');
        const trending = await trendingRes.json();

        const trendingContainer = document.getElementById('trending-sounds');

        if (trending && Array.isArray(trending.sounds)) {
            trendingContainer.innerHTML = trending.sounds.map(sound => createSoundCard(sound)).join('');
        } else {
            trendingContainer.innerHTML = '<p class="text-center text-dark-500 py-8">Aucun son tendance disponible</p>';
        }
    } catch (error) {
        console.error('Failed to load trending sounds:', error);
        document.getElementById('trending-sounds').innerHTML = '<p class="text-center text-red-500 py-8">Erreur de chargement</p>';
    }

    // Load recent sounds
    try {
        const recentRes = await fetch('/wp-json/arborisis/v1/sounds?orderby=recent&per_page=8');
        const recent = await recentRes.json();

        const recentContainer = document.getElementById('recent-sounds');

        if (recent && Array.isArray(recent.sounds)) {
            recentContainer.innerHTML = recent.sounds.map(sound => createSoundCard(sound)).join('');
        } else {
            recentContainer.innerHTML = '<p class="text-center text-dark-500 py-8">Aucun son récent disponible</p>';
        }
    } catch (error) {
        console.error('Failed to load recent sounds:', error);
        document.getElementById('recent-sounds').innerHTML = '<p class="text-center text-red-500 py-8">Erreur de chargement</p>';
    }
});

function createSoundCard(sound) {
    const imageUrl = sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.svg';
    const duration = formatDuration(sound.duration);

    return `
        <a href="/sound/${sound.id}" class="sound-card">
            <div class="sound-card-image">
                <img src="${imageUrl}" alt="${sound.title}" loading="lazy">
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
                <p class="text-sm text-dark-600 dark:text-dark-400 mb-3 line-clamp-2">${sound.description || ''}</p>
                <div class="flex items-center justify-between text-xs text-dark-500 dark:text-dark-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ${duration}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ${sound.plays || sound.plays_count || 0}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        ${sound.likes || sound.likes_count || 0}
                    </span>
                </div>
                ${sound.tags && sound.tags.length ? `
                    <div class="flex flex-wrap gap-1 mt-3">
                        ${sound.tags.slice(0, 3).map(tag => `<span class="badge badge-primary">${tag}</span>`).join('')}
                    </div>
                ` : ''}
            </div>
        </a>
    `;
}

function formatDuration(seconds) {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}
</script>

<?php get_footer(); ?>
