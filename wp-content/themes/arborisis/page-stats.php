<?php
/**
 * Template Name: Stats
 * Description: Statistics dashboard with leaderboards and analytics
 */

get_header();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Statistiques
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400 max-w-2xl">
                Découvrez les tendances, les sons les plus populaires et les contributeurs actifs de la communauté.
            </p>
        </div>

        <!-- Global Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400" id="stat-sounds">-</div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Enregistrements
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400" id="stat-plays">-</div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Écoutes totales
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400" id="stat-users">-</div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Contributeurs
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400" id="stat-countries">-</div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Pays couverts
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Chart -->
        <div class="card mb-12">
            <div class="card-body">
                <h2 class="text-2xl font-display font-bold mb-6">Activité sur 30 jours</h2>
                <div id="timeline-chart" class="h-64">
                    <!-- Chart will be rendered here -->
                    <div class="flex items-center justify-center h-full text-dark-400">
                        Chargement du graphique...
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

            <!-- Top Sounds -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-display font-bold">🏆 Top Sons</h2>
                        <select id="sounds-period" class="input w-32 text-sm">
                            <option value="7d">7 jours</option>
                            <option value="30d" selected>30 jours</option>
                            <option value="all">Tout</option>
                        </select>
                    </div>

                    <div id="top-sounds" class="space-y-3">
                        <!-- Loading skeletons -->
                        <?php for ($i = 0; $i < 10; $i++) : ?>
                            <div class="skeleton h-16"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-display font-bold">👥 Top Contributeurs</h2>
                        <select id="users-period" class="input w-32 text-sm">
                            <option value="7d">7 jours</option>
                            <option value="30d" selected>30 jours</option>
                            <option value="all">Tout</option>
                        </select>
                    </div>

                    <div id="top-users" class="space-y-3">
                        <!-- Loading skeletons -->
                        <?php for ($i = 0; $i < 10; $i++) : ?>
                            <div class="skeleton h-16"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Tag Cloud -->
        <div class="card mb-12">
            <div class="card-body">
                <h2 class="text-2xl font-display font-bold mb-6">☁️ Nuage de Tags</h2>
                <div id="tag-cloud" class="flex flex-wrap gap-3 justify-center py-8">
                    <!-- Populated by JS -->
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-body">
                <h2 class="text-2xl font-display font-bold mb-6">📊 Activité Récente</h2>
                <div id="recent-activity" class="space-y-4">
                    <!-- Populated by JS -->
                    <?php for ($i = 0; $i < 5; $i++) : ?>
                        <div class="skeleton h-20"></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Load global stats
async function loadGlobalStats() {
    try {
        const response = await fetch('/wp-json/arborisis/v1/stats/global');
        const stats = await response.json();

        document.getElementById('stat-sounds').textContent = stats.total_sounds?.toLocaleString() || '0';
        document.getElementById('stat-plays').textContent = stats.total_plays?.toLocaleString() || '0';
        document.getElementById('stat-users').textContent = stats.total_users?.toLocaleString() || '0';
        document.getElementById('stat-countries').textContent = stats.countries_count?.toLocaleString() || '0';

        // Render timeline
        if (stats.timeline) {
            renderTimeline(stats.timeline);
        }
    } catch (error) {
        console.error('Failed to load global stats:', error);
    }
}

// Load leaderboards
async function loadLeaderboards(type = 'sounds', period = '30d') {
    try {
        const response = await fetch(`/wp-json/arborisis/v1/stats/leaderboards?type=${type}&period=${period}`);
        const data = await response.json();

        if (type === 'sounds') {
            renderTopSounds(data.sounds || data);
        } else {
            renderTopUsers(data.users || data);
        }
    } catch (error) {
        console.error('Failed to load leaderboards:', error);
    }
}

function renderTopSounds(sounds) {
    const container = document.getElementById('top-sounds');
    if (!sounds || sounds.length === 0) {
        container.innerHTML = '<p class="text-center text-dark-500 py-8">Aucune donnée disponible</p>';
        return;
    }

    container.innerHTML = sounds.map((sound, index) => `
        <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-dark-50 dark:hover:bg-dark-800 transition-colors">
            <div class="text-2xl font-bold ${index < 3 ? 'text-primary-600' : 'text-dark-400'} w-8 text-center">
                ${index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : index + 1}
            </div>
            <img src="${sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.jpg'}"
                 alt="${sound.title}"
                 class="w-12 h-12 rounded-lg object-cover">
            <div class="flex-1 min-w-0">
                <a href="/sound/${sound.id}" class="font-medium hover:text-primary-600 transition-colors truncate block">
                    ${sound.title}
                </a>
                <div class="text-xs text-dark-500">
                    ${sound.plays_count || 0} plays • ${sound.likes_count || 0} likes
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-primary-600">${sound.plays_count || 0}</div>
                <div class="text-xs text-dark-500">plays</div>
            </div>
        </div>
    `).join('');
}

function renderTopUsers(users) {
    const container = document.getElementById('top-users');
    if (!users || users.length === 0) {
        container.innerHTML = '<p class="text-center text-dark-500 py-8">Aucune donnée disponible</p>';
        return;
    }

    container.innerHTML = users.map((user, index) => `
        <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-dark-50 dark:hover:bg-dark-800 transition-colors">
            <div class="text-2xl font-bold ${index < 3 ? 'text-primary-600' : 'text-dark-400'} w-8 text-center">
                ${index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : index + 1}
            </div>
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-lg">
                ${user.name?.charAt(0).toUpperCase() || '?'}
            </div>
            <div class="flex-1 min-w-0">
                <a href="/profile/${user.username}" class="font-medium hover:text-primary-600 transition-colors truncate block">
                    ${user.name}
                </a>
                <div class="text-xs text-dark-500">
                    ${user.sounds_count || 0} sons • ${user.total_plays || 0} plays
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-bold text-primary-600">${user.total_plays || 0}</div>
                <div class="text-xs text-dark-500">plays</div>
            </div>
        </div>
    `).join('');
}

function renderTimeline(timeline) {
    const container = document.getElementById('timeline-chart');

    // Simple bar chart with SVG
    const maxValue = Math.max(...timeline.map(d => d.plays || 0));
    const width = container.clientWidth;
    const height = 256;
    const barWidth = width / timeline.length - 2;

    const svg = `
        <svg width="${width}" height="${height}" class="w-full">
            ${timeline.map((day, i) => {
                const barHeight = (day.plays / maxValue) * (height - 40);
                const x = i * (barWidth + 2);
                const y = height - barHeight - 20;
                return `
                    <rect x="${x}" y="${y}" width="${barWidth}" height="${barHeight}"
                          fill="currentColor" class="text-primary-500 hover:text-primary-600 transition-colors"
                          rx="2"/>
                    <text x="${x + barWidth/2}" y="${height - 5}" text-anchor="middle"
                          class="text-xs fill-dark-500">
                        ${new Date(day.date).getDate()}
                    </text>
                `;
            }).join('')}
        </svg>
    `;

    container.innerHTML = svg;
}

// Load tag cloud
async function loadTagCloud() {
    try {
        const response = await fetch('/wp-json/wp/v2/sound_tag?per_page=50&orderby=count');
        const tags = await response.json();

        const container = document.getElementById('tag-cloud');
        container.innerHTML = tags.map(tag => {
            const size = Math.min(Math.max(tag.count / 10, 0.8), 2.5);
            return `
                <a href="/explore?tag=${tag.slug}"
                   class="hover:text-primary-600 transition-colors"
                   style="font-size: ${size}rem; opacity: ${0.5 + (tag.count / 100)}">
                    ${tag.name}
                </a>
            `;
        }).join('');
    } catch (error) {
        console.error('Failed to load tag cloud:', error);
    }
}

// Load recent activity
async function loadRecentActivity() {
    try {
        const response = await fetch('/wp-json/arborisis/v1/sounds?orderby=recent&per_page=5');
        const data = await response.json();

        const container = document.getElementById('recent-activity');
        const sounds = data.sounds || data;

        if (!sounds || sounds.length === 0) {
            container.innerHTML = '<p class="text-center text-dark-500 py-8">Aucune activité récente</p>';
            return;
        }

        container.innerHTML = sounds.map(sound => `
            <div class="flex items-center gap-4 p-4 rounded-lg bg-dark-50 dark:bg-dark-800">
                <img src="${sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.jpg'}"
                     alt="${sound.title}"
                     class="w-16 h-16 rounded-lg object-cover">
                <div class="flex-1">
                    <a href="/sound/${sound.id}" class="font-medium hover:text-primary-600 transition-colors">
                        ${sound.title}
                    </a>
                    <div class="text-sm text-dark-600 dark:text-dark-400">
                        Par <a href="/profile/${sound.author_username}" class="hover:text-primary-600">${sound.author || 'Unknown'}</a>
                        • ${window.formatDate(sound.date)}
                    </div>
                </div>
                <div class="text-xs text-dark-500">
                    ${sound.plays_count || 0} plays
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load recent activity:', error);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadGlobalStats();
    loadLeaderboards('sounds', '30d');
    loadLeaderboards('users', '30d');
    loadTagCloud();
    loadRecentActivity();

    // Period selectors
    document.getElementById('sounds-period').addEventListener('change', (e) => {
        loadLeaderboards('sounds', e.target.value);
    });

    document.getElementById('users-period').addEventListener('change', (e) => {
        loadLeaderboards('users', e.target.value);
    });
});
</script>

<?php get_footer(); ?>
