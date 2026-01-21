<?php
/**
 * Template Name: Explore
 * Description: Browse and filter sounds with advanced search
 */

get_header();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Explorer les Sons
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400 max-w-2xl">
                Parcourez notre collection d'enregistrements field recording. Utilisez les filtres pour affiner votre recherche.
            </p>
        </div>

        <!-- Filters & Results -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Left Sidebar: Filters -->
            <aside class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">

                    <!-- Search -->
                    <div class="card">
                        <div class="card-body">
                            <label class="block text-sm font-medium mb-2">Recherche</label>
                            <input
                                type="search"
                                id="filter-search"
                                class="input"
                                placeholder="Mots-clés..."
                            >
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="card">
                        <div class="card-body">
                            <label class="block text-sm font-medium mb-3">Trier par</label>
                            <select id="filter-sort" class="input">
                                <option value="recent">Plus récents</option>
                                <option value="trending">Tendances</option>
                                <option value="popular">Plus populaires</option>
                                <option value="random">Aléatoire</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tags Filter -->
                    <div class="card">
                        <div class="card-body">
                            <label class="block text-sm font-medium mb-3">Tags populaires</label>
                            <div id="popular-tags" class="flex flex-wrap gap-2">
                                <!-- Populated by JS -->
                                <div class="skeleton h-6 w-20"></div>
                                <div class="skeleton h-6 w-24"></div>
                                <div class="skeleton h-6 w-16"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Duration Filter -->
                    <div class="card">
                        <div class="card-body">
                            <label class="block text-sm font-medium mb-3">Durée</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="duration-filter" value="0-30">
                                    <span class="text-sm">Moins de 30s</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="duration-filter" value="30-120">
                                    <span class="text-sm">30s - 2min</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="duration-filter" value="120-300">
                                    <span class="text-sm">2min - 5min</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="duration-filter" value="300+">
                                    <span class="text-sm">Plus de 5min</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- License Filter -->
                    <div class="card">
                        <div class="card-body">
                            <label class="block text-sm font-medium mb-3">Licence</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="license-filter" value="cc0">
                                    <span class="text-sm">CC0 (Domaine public)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="license-filter" value="cc-by">
                                    <span class="text-sm">CC BY</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="license-filter" value="cc-by-sa">
                                    <span class="text-sm">CC BY-SA</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Reset Filters -->
                    <button id="reset-filters" class="btn btn-outline w-full">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Réinitialiser
                    </button>

                </div>
            </aside>

            <!-- Right: Results Grid -->
            <div class="lg:col-span-3">

                <!-- Results Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="text-sm text-dark-600 dark:text-dark-400">
                        <span id="results-count">0</span> enregistrements trouvés
                    </div>

                    <!-- View Toggle -->
                    <div class="flex gap-2">
                        <button id="view-grid" class="btn btn-sm btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>
                        <button id="view-list" class="btn btn-sm btn-ghost">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Results Grid -->
                <div id="results-grid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    <!-- Loading skeleton -->
                    <?php for ($i = 0; $i < 9; $i++) : ?>
                        <div class="skeleton-card"></div>
                    <?php endfor; ?>
                </div>

                <!-- Pagination -->
                <div id="pagination" class="flex items-center justify-center gap-2">
                    <!-- Populated by JS -->
                </div>

                <!-- Load More Button -->
                <div class="text-center mt-8">
                    <button id="load-more" class="btn btn-outline btn-lg">
                        Charger plus
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>

<script>
let currentPage = 1;
let currentFilters = {
    search: '',
    sort: 'recent',
    tags: [],
    duration: [],
    license: [],
};
let isGridView = true;
let totalPages = 1;

// Load sounds with filters
async function loadSounds(page = 1, append = false) {
    try {
        const params = {
            page,
            per_page: 12,
            orderby: currentFilters.sort,
        };

        if (currentFilters.search) params.search = currentFilters.search;
        if (currentFilters.tags.length) params.tags = currentFilters.tags.join(',');

        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`/wp-json/arborisis/v1/sounds?${queryString}`);
        const sounds = await response.json();

        // Get total from headers
        const total = parseInt(response.headers.get('X-WP-Total') || 0);
        totalPages = Math.ceil(total / 12);

        // Update count
        document.getElementById('results-count').textContent = total;

        // Render results
        const grid = document.getElementById('results-grid');
        const html = sounds.map(sound => createSoundCard(sound)).join('');

        if (append) {
            grid.innerHTML += html;
        } else {
            grid.innerHTML = html;
        }

        // Update pagination
        renderPagination(page, totalPages);

        // Show/hide load more
        const loadMoreBtn = document.getElementById('load-more');
        if (page >= totalPages) {
            loadMoreBtn.style.display = 'none';
        } else {
            loadMoreBtn.style.display = 'inline-flex';
        }

    } catch (error) {
        console.error('Failed to load sounds:', error);
    }
}

// Create sound card HTML
function createSoundCard(sound) {
    if (isGridView) {
        return createGridCard(sound);
    } else {
        return createListCard(sound);
    }
}

function createGridCard(sound) {
    const imageUrl = sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.jpg';
    const duration = window.formatDuration(sound.duration);

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
                <div class="flex items-center justify-between text-xs text-dark-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ${duration}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        </svg>
                        ${sound.plays_count || 0}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        ${sound.likes_count || 0}
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

function createListCard(sound) {
    const imageUrl = sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.jpg';
    const duration = window.formatDuration(sound.duration);

    return `
        <a href="/sound/${sound.id}" class="card hover:shadow-xl transition-shadow">
            <div class="card-body p-4">
                <div class="flex gap-4">
                    <img src="${imageUrl}" alt="${sound.title}" class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-lg mb-1 truncate">${sound.title}</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400 mb-2 line-clamp-2">${sound.description || ''}</p>
                        <div class="flex items-center gap-4 text-xs text-dark-500">
                            <span>${duration}</span>
                            <span>${sound.plays_count || 0} plays</span>
                            <span>${sound.likes_count || 0} likes</span>
                            ${sound.location_name ? `<span>📍 ${sound.location_name}</span>` : ''}
                        </div>
                        ${sound.tags && sound.tags.length ? `
                            <div class="flex flex-wrap gap-1 mt-2">
                                ${sound.tags.slice(0, 5).map(tag => `<span class="badge badge-primary">${tag}</span>`).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        </a>
    `;
}

function renderPagination(current, total) {
    const pagination = document.getElementById('pagination');
    if (total <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';

    // Previous
    if (current > 1) {
        html += `<button onclick="goToPage(${current - 1})" class="btn btn-ghost btn-sm">← Précédent</button>`;
    }

    // Page numbers
    for (let i = 1; i <= total; i++) {
        if (i === current) {
            html += `<button class="btn btn-primary btn-sm">${i}</button>`;
        } else if (i === 1 || i === total || Math.abs(i - current) <= 2) {
            html += `<button onclick="goToPage(${i})" class="btn btn-ghost btn-sm">${i}</button>`;
        } else if (i === current - 3 || i === current + 3) {
            html += `<span class="px-2">...</span>`;
        }
    }

    // Next
    if (current < total) {
        html += `<button onclick="goToPage(${current + 1})" class="btn btn-ghost btn-sm">Suivant →</button>`;
    }

    pagination.innerHTML = html;
}

window.goToPage = function(page) {
    currentPage = page;
    loadSounds(page);
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Initialize
document.addEventListener('DOMContentLoaded', async () => {
    // Load initial sounds
    loadSounds(1);

    // Load popular tags
    try {
        const response = await fetch('/wp-json/wp/v2/sound_tag?per_page=20&orderby=count');
        const tags = await response.json();
        const container = document.getElementById('popular-tags');
        container.innerHTML = tags.map(tag => `
            <button class="badge badge-primary cursor-pointer hover:scale-105 transition-transform tag-filter" data-tag="${tag.slug}">
                ${tag.name}
            </button>
        `).join('');

        // Tag click handlers
        container.querySelectorAll('.tag-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                const tag = btn.dataset.tag;
                if (currentFilters.tags.includes(tag)) {
                    currentFilters.tags = currentFilters.tags.filter(t => t !== tag);
                    btn.classList.remove('badge-secondary');
                } else {
                    currentFilters.tags.push(tag);
                    btn.classList.add('badge-secondary');
                }
                loadSounds(1);
            });
        });
    } catch (error) {
        console.error('Failed to load tags:', error);
    }

    // Search filter
    let searchTimeout;
    document.getElementById('filter-search').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentFilters.search = e.target.value;
            loadSounds(1);
        }, 500);
    });

    // Sort filter
    document.getElementById('filter-sort').addEventListener('change', (e) => {
        currentFilters.sort = e.target.value;
        loadSounds(1);
    });

    // View toggle
    document.getElementById('view-grid').addEventListener('click', () => {
        isGridView = true;
        document.getElementById('view-grid').classList.add('btn-primary');
        document.getElementById('view-grid').classList.remove('btn-ghost');
        document.getElementById('view-list').classList.remove('btn-primary');
        document.getElementById('view-list').classList.add('btn-ghost');
        document.getElementById('results-grid').className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8';
        loadSounds(currentPage);
    });

    document.getElementById('view-list').addEventListener('click', () => {
        isGridView = false;
        document.getElementById('view-list').classList.add('btn-primary');
        document.getElementById('view-list').classList.remove('btn-ghost');
        document.getElementById('view-grid').classList.remove('btn-primary');
        document.getElementById('view-grid').classList.add('btn-ghost');
        document.getElementById('results-grid').className = 'space-y-4 mb-8';
        loadSounds(currentPage);
    });

    // Reset filters
    document.getElementById('reset-filters').addEventListener('click', () => {
        currentFilters = {
            search: '',
            sort: 'recent',
            tags: [],
            duration: [],
            license: [],
        };
        document.getElementById('filter-search').value = '';
        document.getElementById('filter-sort').value = 'recent';
        document.querySelectorAll('.tag-filter').forEach(btn => btn.classList.remove('badge-secondary'));
        document.querySelectorAll('.duration-filter').forEach(cb => cb.checked = false);
        document.querySelectorAll('.license-filter').forEach(cb => cb.checked = false);
        loadSounds(1);
    });

    // Load more
    document.getElementById('load-more').addEventListener('click', () => {
        currentPage++;
        loadSounds(currentPage, true);
    });
});
</script>

<?php get_footer(); ?>
