<?php
/**
 * Template Name: Profile
 * Description: User profile page (use with rewrite rule for /profile/username)
 */

// Get username from URL
$username = get_query_var('username');
if (!$username && isset($_GET['username'])) {
    $username = sanitize_text_field($_GET['username']);
}

// Get user
$user = get_user_by('login', $username);
if (!$user) {
    wp_redirect(home_url('/404'));
    exit;
}

$user_id = $user->ID;
$is_own_profile = is_user_logged_in() && get_current_user_id() === $user_id;

// Get user meta
$bio = get_user_meta($user_id, '_arb_bio', true);
$website = get_user_meta($user_id, '_arb_website', true);
$twitter = get_user_meta($user_id, '_arb_twitter', true);
$instagram = get_user_meta($user_id, '_arb_instagram', true);
$total_plays = get_user_meta($user_id, '_arb_total_plays', true) ?: 0;
$total_likes = get_user_meta($user_id, '_arb_total_likes', true) ?: 0;

get_header();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom max-w-6xl">

        <!-- Profile Header -->
        <div class="card mb-8">
            <div class="card-body">
                <div class="flex flex-col md:flex-row gap-8">

                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        <?php echo get_avatar($user_id, 160, '', '', ['class' => 'rounded-full']); ?>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h1
                                    class="text-3xl md:text-4xl font-display font-bold text-dark-900 dark:text-dark-50 mb-2">
                                    <?php echo esc_html($user->display_name); ?>
                                </h1>
                                <p class="text-dark-600 dark:text-dark-400">
                                    @<?php echo esc_html($user->user_login); ?>
                                </p>
                            </div>

                            <?php if ($is_own_profile): ?>
                                <a href="/profile/edit" class="btn btn-outline">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Éditer le profil
                                </a>
                            <?php endif; ?>
                        </div>

                        <?php if ($bio): ?>
                            <p class="text-dark-700 dark:text-dark-300 mb-6 leading-relaxed">
                                <?php echo nl2br(esc_html($bio)); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="text-center p-4 bg-dark-50 dark:bg-dark-800 rounded-lg">
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400"
                                    id="sounds-count">-</div>
                                <div class="text-xs uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-1">Sons
                                </div>
                            </div>
                            <div class="text-center p-4 bg-dark-50 dark:bg-dark-800 rounded-lg">
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    <?php echo number_format($total_plays); ?></div>
                                <div class="text-xs uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-1">
                                    Écoutes</div>
                            </div>
                            <div class="text-center p-4 bg-dark-50 dark:bg-dark-800 rounded-lg">
                                <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    <?php echo number_format($total_likes); ?></div>
                                <div class="text-xs uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-1">Likes
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="flex flex-wrap gap-3">
                            <?php if ($website): ?>
                                <a href="<?php echo esc_url($website); ?>" target="_blank" class="btn btn-ghost btn-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    Site web
                                </a>
                            <?php endif; ?>
                            <?php if ($twitter): ?>
                                <a href="https://twitter.com/<?php echo esc_attr($twitter); ?>" target="_blank"
                                    class="btn btn-ghost btn-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z" />
                                    </svg>
                                    Twitter
                                </a>
                            <?php endif; ?>
                            <?php if ($instagram): ?>
                                <a href="https://instagram.com/<?php echo esc_attr($instagram); ?>" target="_blank"
                                    class="btn btn-ghost btn-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                                        <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01" />
                                    </svg>
                                    Instagram
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-8">
            <div class="flex gap-2 border-b border-dark-200 dark:border-dark-700">
                <button class="tab-btn px-6 py-3 font-medium border-b-2 border-primary-600 text-primary-600"
                    data-tab="sounds">
                    Enregistrements
                </button>
                <button
                    class="tab-btn px-6 py-3 font-medium border-b-2 border-transparent text-dark-600 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-50"
                    data-tab="stats">
                    Statistiques
                </button>
                <?php if ($is_own_profile): ?>
                    <button
                        class="tab-btn px-6 py-3 font-medium border-b-2 border-transparent text-dark-600 dark:text-dark-400 hover:text-dark-900 dark:hover:text-dark-50"
                        data-tab="favorites">
                        Favoris
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab Content -->
        <div id="tab-sounds" class="tab-content">
            <div id="user-sounds" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Populated by JS -->
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <div class="skeleton-card"></div>
                <?php endfor; ?>
            </div>
        </div>

        <div id="tab-stats" class="tab-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Top Sounds -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-xl font-display font-bold mb-4">Sons les plus populaires</h2>
                        <div id="user-top-sounds" class="space-y-3">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-xl font-display font-bold mb-4">Activité récente</h2>
                        <div id="user-activity" class="space-y-3">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($is_own_profile): ?>
            <div id="tab-favorites" class="tab-content hidden">
                <div id="user-favorites" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Populated by JS -->
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    const userId = <?php echo $user_id; ?>;
    const username = '<?php echo esc_js($user->user_login); ?>';

    // Load user sounds
    async function loadUserSounds() {
        try {
            const response = await fetch(`/wp-json/arborisis/v1/sounds?author=${userId}&per_page=12`);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            const sounds = data.sounds || []; // Access nested sounds array

            // Update count
            document.getElementById('sounds-count').textContent = sounds.length;

            const container = document.getElementById('user-sounds');
            if (sounds.length === 0) {
                container.innerHTML = '<p class="col-span-3 text-center text-dark-500 py-12">Aucun enregistrement pour le moment</p>';
                return;
            }

            container.innerHTML = sounds.map(sound => createSoundCard(sound)).join('');
        } catch (error) {
            console.error('Failed to load user sounds:', error);
        }
    }

    function createSoundCard(sound) {
        return `
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
                    <span>${sound.likes_count || 0} likes</span>
                </div>
            </div>
        </a>
    `;
    }

    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;

            // Update buttons
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('border-primary-600', 'text-primary-600');
                b.classList.add('border-transparent', 'text-dark-600', 'dark:text-dark-400');
            });
            btn.classList.add('border-primary-600', 'text-primary-600');
            btn.classList.remove('border-transparent', 'text-dark-600', 'dark:text-dark-400');

            // Show content
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(`tab-${tab}`).classList.remove('hidden');
        });
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        loadUserSounds();
    });
</script>

<?php get_footer(); ?>