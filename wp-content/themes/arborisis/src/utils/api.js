/**
 * @typedef {import('../types.js').Sound} Sound
 * @typedef {import('../types.js').User} User
 * @typedef {import('../types.js').APIResponse} APIResponse
 * @typedef {import('../types.js').LeaderboardResponse} LeaderboardResponse
 * @typedef {import('../types.js').GlobalStats} GlobalStats
 */

/**
 * Fetch sounds from API with validation
 * @param {string} url - API endpoint URL
 * @returns {Promise<APIResponse>}
 */
export async function fetchSounds(url) {
    try {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();

        // Runtime validation
        if (!data.sounds || !Array.isArray(data.sounds)) {
            console.warn('Invalid API response format, expected {sounds: [], total: number}', data);
            return { sounds: [], total: 0, pages: 0 };
        }

        return data;
    } catch (error) {
        console.error('Failed to fetch sounds:', error);
        return { sounds: [], total: 0, pages: 0 };
    }
}

/**
 * Fetch leaderboards with validation
 * @param {'sounds'|'users'} type - Leaderboard type
 * @param {'7d'|'30d'|'all'} period - Time period
 * @returns {Promise<LeaderboardResponse>}
 */
export async function fetchLeaderboard(type, period = '30d') {
    try {
        const response = await fetch(`/wp-json/arborisis/v1/stats/leaderboards?type=${type}&period=${period}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        // Normalize response - handle both array and object responses
        if (type === 'sounds') {
            return {
                sounds: data.sounds || (Array.isArray(data) ? data : []),
                users: []
            };
        } else {
            return {
                sounds: [],
                users: data.users || (Array.isArray(data) ? data : [])
            };
        }
    } catch (error) {
        console.error('Failed to fetch leaderboard:', error);
        return { sounds: [], users: [] };
    }
}

/**
 * Fetch global statistics
 * @returns {Promise<GlobalStats|null>}
 */
export async function fetchGlobalStats() {
    try {
        const response = await fetch('/wp-json/arborisis/v1/stats/global');

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Failed to fetch global stats:', error);
        return null;
    }
}

/**
 * Fetch single sound by ID
 * @param {number} id - Sound ID
 * @returns {Promise<Sound|null>}
 */
export async function fetchSound(id) {
    try {
        const response = await fetch(`/wp-json/arborisis/v1/sounds/${id}`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`Failed to fetch sound ${id}:`, error);
        return null;
    }
}

/**
 * Like a sound
 * @param {number} soundId - Sound ID
 * @returns {Promise<{success: boolean, liked: boolean}>}
 */
export async function toggleLike(soundId) {
    try {
        const response = await fetch(`/wp-json/arborisis/v1/sounds/${soundId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': window.arborisisData?.nonce || ''
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`Failed to toggle like for sound ${soundId}:`, error);
        return { success: false, liked: false };
    }
}

/**
 * Record a play event
 * @param {number} soundId - Sound ID
 * @returns {Promise<{success: boolean}>}
 */
export async function recordPlay(soundId) {
    try {
        const response = await fetch(`/wp-json/arborisis/v1/sounds/${soundId}/play`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`Failed to record play for sound ${soundId}:`, error);
        return { success: false };
    }
}

/**
 * Generic fetch with error handling
 * @param {string} url - URL to fetch
 * @param {HTMLElement|null} errorContainer - Element to display errors
 * @returns {Promise<any|null>}
 */
export async function fetchWithErrorHandling(url, errorContainer = null) {
    try {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`Fetch error for ${url}:`, error);

        if (errorContainer) {
            errorContainer.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-500 mb-4">Erreur de chargement</p>
                    <button onclick="location.reload()" class="btn btn-primary">
                        Réessayer
                    </button>
                </div>
            `;
        }

        return null;
    }
}
