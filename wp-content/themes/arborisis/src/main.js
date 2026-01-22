/**
 * Main JavaScript entry point
 * Global functionality, search, dark mode, etc.
 */

import './styles/main.css';

// Initialize dark mode
function initDarkMode() {
    const theme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (theme === 'dark' || (!theme && prefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

// Initialize on load
initDarkMode();

// API Client
class ArbAPI {
    constructor() {
        this.baseUrl = window.arbData?.apiUrl || '/wp-json/arborisis/v1';
        this.nonce = window.arbData?.nonce || '';
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const headers = {
            'Content-Type': 'application/json',
            ...(this.nonce && { 'X-WP-Nonce': this.nonce }),
            ...options.headers,
        };

        try {
            const response = await fetch(url, {
                ...options,
                headers,
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }

    // Sounds
    async getSounds(params = {}) {
        const query = new URLSearchParams(params).toString();
        return this.request(`/sounds${query ? '?' + query : ''}`);
    }

    async getSound(id) {
        return this.request(`/sounds/${id}`);
    }

    // Search
    async search(query, params = {}) {
        const allParams = { q: query, ...params };
        const queryString = new URLSearchParams(allParams).toString();
        return this.request(`/search?${queryString}`);
    }

    // Stats
    async trackPlay(soundId) {
        return this.request(`/sounds/${soundId}/play`, {
            method: 'POST',
        });
    }

    async toggleLike(soundId) {
        return this.request(`/sounds/${soundId}/like`, {
            method: 'POST',
        });
    }

    async getGlobalStats() {
        return this.request('/stats/global');
    }

    async getUserStats(userId) {
        return this.request(`/stats/user/${userId}`);
    }

    async getLeaderboards(type = 'sounds', period = '7d') {
        return this.request(`/stats/leaderboards?type=${type}&period=${period}`);
    }

    // Map
    async getMapSounds(bbox, zoom) {
        const [lat1, lon1, lat2, lon2] = bbox;
        return this.request(`/map/sounds?bbox=${lat1},${lon1},${lat2},${lon2}&zoom=${zoom}`);
    }

    // Graph
    async exploreGraph(seedId, depth = 2, maxNodes = 50) {
        return this.request(`/graph/explore?seed_id=${seedId}&depth=${depth}&max_nodes=${maxNodes}`);
    }
}

// Initialize global API client
window.ArbAPI = new ArbAPI();

// Global search functionality
class GlobalSearch {
    constructor() {
        this.searchInput = document.getElementById('global-search');
        this.searchModal = document.getElementById('search-modal');
        this.searchResults = document.getElementById('search-results');
        this.searchTimeout = null;

        this.init();
    }

    init() {
        if (!this.searchInput) return;

        this.searchInput.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                this.showResults([]);
                return;
            }

            this.searchTimeout = setTimeout(() => this.performSearch(query), 300);
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Cmd/Ctrl + K to open search
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                this.openModal();
            }

            // Escape to close
            if (e.key === 'Escape' && !this.searchModal?.classList.contains('hidden')) {
                this.closeModal();
            }
        });
    }

    openModal() {
        this.searchModal?.classList.remove('hidden');
        this.searchInput?.focus();
    }

    closeModal() {
        this.searchModal?.classList.add('hidden');
        if (this.searchInput) this.searchInput.value = '';
        this.showResults([]);
    }

    async performSearch(query) {
        try {
            const response = await window.ArbAPI.search(query, { per_page: 8 });
            // API returns { sounds: [...], total, meta }
            const results = response.sounds || response;
            if (!Array.isArray(results)) {
                console.error('Invalid search response:', response);
                this.showResults([]);
                return;
            }
            this.showResults(results);
        } catch (error) {
            console.error('Search failed:', error);
            this.showResults([]);
        }
    }

    showResults(results) {
        if (!this.searchResults) return;

        if (results.length === 0) {
            this.searchResults.innerHTML = `
                <div class="p-8 text-center text-dark-500 dark:text-dark-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-dark-300 dark:text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p>Aucun résultat trouvé</p>
                </div>
            `;
            return;
        }

        this.searchResults.innerHTML = results.map(sound => this.createResultCard(sound)).join('');
    }

    createResultCard(sound) {
        return `
            <a href="/sound/${sound.id}" class="block p-4 hover:bg-dark-50 dark:hover:bg-dark-700 transition-colors border-b border-dark-200 dark:border-dark-700 last:border-0">
                <div class="flex gap-3">
                    <img src="${sound.thumbnail || '/wp-content/themes/arborisis/assets/placeholder.svg'}"
                         alt="${sound.title}"
                         class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-dark-900 dark:text-dark-50 truncate">${sound.title}</h3>
                        <p class="text-sm text-dark-600 dark:text-dark-400 line-clamp-1">${sound.description || ''}</p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-dark-500">
                            <span>${sound.author || 'Unknown'}</span>
                            ${sound.location_name ? `<span>📍 ${sound.location_name}</span>` : ''}
                        </div>
                        ${sound.tags && sound.tags.length ? `
                            <div class="flex flex-wrap gap-1 mt-2">
                                ${sound.tags.slice(0, 3).map(tag => `<span class="badge badge-primary">${tag}</span>`).join('')}
                            </div>
                        ` : ''}
                    </div>
                </div>
            </a>
        `;
    }
}

// Initialize global search
document.addEventListener('DOMContentLoaded', () => {
    new GlobalSearch();
});

// Utility functions
window.formatDuration = function (seconds) {
    if (!seconds) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

window.formatNumber = function (num) {
    if (!num) return '0';
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M';
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K';
    return num.toString();
};

window.formatDate = function (date) {
    const d = new Date(date);
    const now = new Date();
    const diff = now - d;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (days === 0) return 'Aujourd\'hui';
    if (days === 1) return 'Hier';
    if (days < 7) return `Il y a ${days} jours`;
    if (days < 30) return `Il y a ${Math.floor(days / 7)} semaines`;
    if (days < 365) return `Il y a ${Math.floor(days / 30)} mois`;
    return `Il y a ${Math.floor(days / 365)} ans`;
};

// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/wp-content/themes/arborisis/src/js/service-worker.js')
            .then(registration => {
                console.log('SW registered:', registration.scope);
            })
            .catch(error => {
                console.log('SW registration failed:', error);
            });
    });
}

// Export for use in other modules
export { ArbAPI };
