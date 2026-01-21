/**
 * Global Keyboard Shortcuts
 * Handles application-wide keyboard navigation and actions
 */

class KeyboardShortcuts {
    constructor() {
        this.shortcuts = new Map();
        this.init();
    }

    init() {
        // Register default shortcuts
        this.register('/', () => this.openSearch());
        this.register('Escape', () => this.closeModals());
        this.register('?', () => this.showHelp());

        // Cmd/Ctrl + K for search
        this.register('k', () => this.openSearch(), { ctrl: true });
        this.register('k', () => this.openSearch(), { meta: true });

        // Media controls
        this.register(' ', () => this.togglePlay(), { target: 'body' });
        this.register('m', () => this.toggleMute());

        // Navigation
        this.register('g h', () => this.navigate('/'));
        this.register('g e', () => this.navigate('/explore'));
        this.register('g m', () => this.navigate('/map'));
        this.register('g g', () => this.navigate('/graph'));

        // Listen for keydown events
        document.addEventListener('keydown', (e) => this.handleKeydown(e));
    }

    /**
     * Register a keyboard shortcut
     * @param {string} key - Key combination (e.g., 'ctrl+k', 'a b', etc.)
     * @param {Function} callback - Function to call when shortcut is triggered
     * @param {Object} options - Additional options (ctrl, meta, shift, alt, target)
     */
    register(key, callback, options = {}) {
        const normalizedKey = key.toLowerCase();
        this.shortcuts.set(normalizedKey, { callback, options });
    }

    handleKeydown(e) {
        // Don't trigger shortcuts when typing in inputs
        const target = e.target.tagName.toLowerCase();
        if (['input', 'textarea', 'select'].includes(target) && e.key !== 'Escape') {
            return;
        }

        // Build the key combination
        const key = e.key.toLowerCase();
        let combination = '';

        if (e.ctrlKey) combination += 'ctrl+';
        if (e.metaKey) combination += 'meta+';
        if (e.shiftKey && key !== 'shift') combination += 'shift+';
        if (e.altKey && key !== 'alt') combination += 'alt+';

        combination += key;

        // Check for registered shortcut
        const shortcut = this.shortcuts.get(key) || this.shortcuts.get(combination);

        if (shortcut) {
            const { callback, options } = shortcut;

            // Check modifiers match
            const modifiersMatch =
                (options.ctrl === undefined || options.ctrl === e.ctrlKey) &&
                (options.meta === undefined || options.meta === e.metaKey) &&
                (options.shift === undefined || options.shift === e.shiftKey) &&
                (options.alt === undefined || options.alt === e.altKey);

            if (modifiersMatch) {
                e.preventDefault();
                callback(e);
            }
        }
    }

    // Shortcut actions
    openSearch() {
        const searchModal = document.getElementById('search-modal');
        if (searchModal) {
            searchModal.classList.remove('hidden');
            const searchInput = searchModal.querySelector('input');
            if (searchInput) searchInput.focus();
        } else {
            // Alternative: focus hero search
            const heroSearch = document.getElementById('hero-search');
            if (heroSearch) heroSearch.focus();
        }
    }

    closeModals() {
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach((modal) => modal.classList.add('hidden'));
    }

    showHelp() {
        alert(`
Keyboard Shortcuts:
─────────────────
/         Open search
Ctrl/⌘ K  Search
Esc       Close modals
Space     Play/pause
M         Mute/unmute
G H       Go to home
G E       Go to explore
G M       Go to map
G G       Go to graph
?         Show this help
    `);
    }

    togglePlay() {
        if (window.arbPlayer) {
            window.arbPlayer.togglePlay();
        }
    }

    toggleMute() {
        if (window.arbPlayer) {
            window.arbPlayer.toggleMute();
        }
    }

    navigate(path) {
        window.location.href = path;
    }

    destroy() {
        this.shortcuts.clear();
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.keyboardShortcuts = new KeyboardShortcuts();
});

export default KeyboardShortcuts;
