    </main>

    <footer class="bg-dark-900 text-dark-300 border-t border-dark-800 mt-20">
        <div class="container-custom py-12">

            <!-- Footer Top -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">

                <!-- About -->
                <div>
                    <h3 class="text-white font-display font-bold text-lg mb-4">À propos</h3>
                    <p class="text-sm leading-relaxed mb-4">
                        <?php bloginfo('description'); ?>
                    </p>
                    <p class="text-sm">
                        Plateforme collaborative de field recording et paysages sonores.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-display font-bold text-lg mb-4">Explorer</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?php echo esc_url(home_url('/explore')); ?>" class="hover:text-primary-400 transition-colors">Parcourir les sons</a></li>
                        <li><a href="<?php echo esc_url(home_url('/map')); ?>" class="hover:text-primary-400 transition-colors">Carte interactive</a></li>
                        <li><a href="<?php echo esc_url(home_url('/graph')); ?>" class="hover:text-primary-400 transition-colors">Exploration graphe</a></li>
                        <li><a href="<?php echo esc_url(home_url('/stats')); ?>" class="hover:text-primary-400 transition-colors">Statistiques</a></li>
                    </ul>
                </div>

                <!-- Community -->
                <div>
                    <h3 class="text-white font-display font-bold text-lg mb-4">Communauté</h3>
                    <ul class="space-y-2 text-sm">
                        <?php if (is_user_logged_in()) : ?>
                            <li><a href="<?php echo esc_url(home_url('/upload')); ?>" class="hover:text-primary-400 transition-colors">Uploader un son</a></li>
                            <li><a href="<?php echo esc_url(home_url('/my-sounds')); ?>" class="hover:text-primary-400 transition-colors">Mes enregistrements</a></li>
                        <?php else : ?>
                            <li><a href="<?php echo wp_registration_url(); ?>" class="hover:text-primary-400 transition-colors">Devenir contributeur</a></li>
                            <li><a href="<?php echo wp_login_url(); ?>" class="hover:text-primary-400 transition-colors">Connexion</a></li>
                        <?php endif; ?>
                        <li><a href="<?php echo esc_url(home_url('/guidelines')); ?>" class="hover:text-primary-400 transition-colors">Recommandations</a></li>
                        <li><a href="<?php echo esc_url(home_url('/licenses')); ?>" class="hover:text-primary-400 transition-colors">Licences</a></li>
                    </ul>
                </div>

                <!-- Contact & Social -->
                <div>
                    <h3 class="text-white font-display font-bold text-lg mb-4">Contact</h3>
                    <ul class="space-y-2 text-sm mb-6">
                        <li><a href="<?php echo esc_url(home_url('/about')); ?>" class="hover:text-primary-400 transition-colors">À propos du projet</a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="hover:text-primary-400 transition-colors">Nous contacter</a></li>
                        <li><a href="<?php echo esc_url(home_url('/privacy')); ?>" class="hover:text-primary-400 transition-colors">Confidentialité</a></li>
                        <li><a href="<?php echo esc_url(home_url('/terms')); ?>" class="hover:text-primary-400 transition-colors">Conditions d'utilisation</a></li>
                    </ul>

                    <!-- Social Links -->
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-dark-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors" aria-label="Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-dark-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                                <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-dark-800 hover:bg-primary-600 rounded-lg flex items-center justify-center transition-colors" aria-label="GitHub">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.462-1.11-1.462-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.646.35-1.086.636-1.336-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0112 6.836c.85.004 1.705.114 2.504.336 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.203 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Footer Stats (Live) -->
            <div class="border-t border-dark-800 pt-8 mb-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="footer-stats">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-400" data-stat="sounds">-</div>
                        <div class="text-xs uppercase tracking-wide mt-1">Enregistrements</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-400" data-stat="users">-</div>
                        <div class="text-xs uppercase tracking-wide mt-1">Contributeurs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-400" data-stat="plays">-</div>
                        <div class="text-xs uppercase tracking-wide mt-1">Écoutes</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-400" data-stat="countries">-</div>
                        <div class="text-xs uppercase tracking-wide mt-1">Pays couverts</div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="border-t border-dark-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
                <div>
                    &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Tous droits réservés.
                </div>

                <div class="flex items-center gap-6">
                    <a href="<?php echo esc_url(home_url('/api-docs')); ?>" class="hover:text-primary-400 transition-colors">
                        Documentation API
                    </a>
                    <a href="<?php echo esc_url(home_url('/rss')); ?>" class="hover:text-primary-400 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6.18 15.64a2.18 2.18 0 012.18 2.18C8.36 19 7.38 20 6.18 20S4 19 4 17.82a2.18 2.18 0 012.18-2.18zM4 4.44A15.56 15.56 0 0119.56 20h-2.83A12.73 12.73 0 004 7.27V4.44zm0 5.66a9.9 9.9 0 019.9 9.9h-2.83A7.07 7.07 0 004 12.93V10.1z"/>
                        </svg>
                        RSS
                    </a>
                    <div class="text-xs text-dark-500">
                        Propulsé par WordPress & OpenSearch
                    </div>
                </div>
            </div>

        </div>
    </footer>

</div><!-- #page -->

<?php wp_footer(); ?>

<!-- Alpine.js for interactive components -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Dark mode toggle
document.getElementById('theme-toggle')?.addEventListener('click', () => {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
});

// Mobile menu toggle
document.getElementById('mobile-menu-toggle')?.addEventListener('click', () => {
    const menu = document.getElementById('mobile-menu');
    menu?.classList.toggle('hidden');
});

// Search modal
const searchToggle = document.getElementById('search-toggle');
const searchModal = document.getElementById('search-modal');

searchToggle?.addEventListener('click', () => {
    searchModal?.classList.remove('hidden');
    document.getElementById('global-search')?.focus();
});

searchModal?.addEventListener('click', (e) => {
    if (e.target === searchModal) {
        searchModal.classList.add('hidden');
    }
});

// Header scroll effect
let lastScroll = 0;
const header = document.getElementById('site-header');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
        header?.classList.add('scrolled');
    } else {
        header?.classList.remove('scrolled');
    }

    lastScroll = currentScroll;
});

// Load footer stats
async function loadFooterStats() {
    try {
        const response = await fetch('/wp-json/arborisis/v1/stats/global');
        const data = await response.json();

        document.querySelector('[data-stat="sounds"]').textContent = data.total_sounds?.toLocaleString() || '0';
        document.querySelector('[data-stat="users"]').textContent = data.total_users?.toLocaleString() || '0';
        document.querySelector('[data-stat="plays"]').textContent = data.total_plays?.toLocaleString() || '0';
        document.querySelector('[data-stat="countries"]').textContent = data.countries_count?.toLocaleString() || '0';
    } catch (error) {
        console.error('Failed to load footer stats:', error);
    }
}

// Load stats on page load
document.addEventListener('DOMContentLoaded', loadFooterStats);
</script>

</body>
</html>
