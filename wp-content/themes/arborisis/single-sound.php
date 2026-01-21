<?php
/**
 * Template for single sound post
 */

get_header();

while (have_posts()) : the_post();

    $sound_id = get_the_ID();
    $audio_url = get_post_meta($sound_id, '_arb_audio_url', true);
    $duration = get_post_meta($sound_id, '_arb_duration', true);
    $format = get_post_meta($sound_id, '_arb_format', true);
    $filesize = get_post_meta($sound_id, '_arb_filesize', true);
    $latitude = get_post_meta($sound_id, '_arb_latitude', true);
    $longitude = get_post_meta($sound_id, '_arb_longitude', true);
    $location_name = get_post_meta($sound_id, '_arb_location_name', true);
    $recorded_at = get_post_meta($sound_id, '_arb_recorded_at', true);
    $equipment = get_post_meta($sound_id, '_arb_equipment', true);
    $plays_count = get_post_meta($sound_id, '_arb_plays_count', true) ?: 0;
    $likes_count = get_post_meta($sound_id, '_arb_likes_count', true) ?: 0;

    $tags = get_the_terms($sound_id, 'sound_tag');
    $license = get_the_terms($sound_id, 'sound_license');
    $author = get_the_author();
    $author_id = get_the_author_meta('ID');
?>

<article id="sound-<?php echo $sound_id; ?>" class="py-8 md:py-16">
    <div class="container-custom max-w-6xl">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-dark-600 dark:text-dark-400 mb-4">
                <a href="/" class="hover:text-primary-600">Accueil</a>
                <span>/</span>
                <a href="/explore" class="hover:text-primary-600">Explorer</a>
                <span>/</span>
                <span class="text-dark-900 dark:text-dark-50"><?php the_title(); ?></span>
            </div>

            <h1 class="text-3xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                <?php the_title(); ?>
            </h1>

            <!-- Author & Date -->
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <a href="/profile/<?php echo get_the_author_meta('user_login'); ?>" class="flex items-center gap-2 hover:text-primary-600 transition-colors">
                    <?php echo get_avatar($author_id, 40, '', '', ['class' => 'rounded-full']); ?>
                    <span class="font-medium"><?php echo $author; ?></span>
                </a>
                <span class="text-dark-500">•</span>
                <time datetime="<?php echo get_the_date('c'); ?>" class="text-dark-600 dark:text-dark-400">
                    <?php echo get_the_date('j F Y'); ?>
                </time>
                <?php if ($location_name) : ?>
                    <span class="text-dark-500">•</span>
                    <span class="flex items-center gap-1 text-dark-600 dark:text-dark-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <?php echo esc_html($location_name); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left: Audio Player & Content -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Audio Player Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Waveform -->
                        <div id="waveform" class="waveform-container mb-6"></div>

                        <!-- Controls -->
                        <div class="flex items-center gap-4 mb-4">
                            <button id="play-btn" class="w-12 h-12 bg-primary-600 hover:bg-primary-700 rounded-full flex items-center justify-center transition-colors">
                                <svg id="play-icon" class="w-6 h-6 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                                <svg id="pause-icon" class="w-6 h-6 text-white hidden" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                                </svg>
                            </button>

                            <div class="flex-1">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span id="current-time" class="text-dark-600 dark:text-dark-400">0:00</span>
                                    <span id="total-time" class="text-dark-600 dark:text-dark-400"><?php echo gmdate('i:s', $duration); ?></span>
                                </div>
                                <input type="range" id="progress-bar" class="w-full h-2 bg-dark-200 dark:bg-dark-700 rounded-full appearance-none cursor-pointer" min="0" max="100" value="0">
                            </div>

                            <button id="volume-btn" class="w-10 h-10 hover:bg-dark-100 dark:hover:bg-dark-800 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                </svg>
                            </button>

                            <a href="<?php echo esc_url($audio_url); ?>" download class="w-10 h-10 hover:bg-dark-100 dark:hover:bg-dark-800 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                        </div>

                        <!-- Stats -->
                        <div class="flex items-center gap-6 pt-4 border-t border-dark-200 dark:border-dark-700">
                            <button id="like-btn" class="flex items-center gap-2 hover:text-red-500 transition-colors" data-sound-id="<?php echo $sound_id; ?>">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                <span id="likes-count"><?php echo number_format($likes_count); ?></span>
                            </button>

                            <span class="flex items-center gap-2 text-dark-600 dark:text-dark-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span id="plays-count"><?php echo number_format($plays_count); ?></span>
                            </span>

                            <button class="flex items-center gap-2 hover:text-primary-600 transition-colors ml-auto">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                Partager
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <?php if (get_the_content()) : ?>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-xl font-display font-bold mb-4">Description</h2>
                        <div class="prose dark:prose-invert max-w-none">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Tags -->
                <?php if ($tags && !is_wp_error($tags)) : ?>
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-xl font-display font-bold mb-4">Tags</h2>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($tags as $tag) : ?>
                                <a href="/explore?tag=<?php echo $tag->slug; ?>" class="badge badge-primary">
                                    <?php echo $tag->name; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Map (if geolocation) -->
                <?php if ($latitude && $longitude) : ?>
                <div class="card">
                    <div class="card-body p-0">
                        <div id="sound-map" class="h-64 rounded-lg" data-lat="<?php echo $latitude; ?>" data-lon="<?php echo $longitude; ?>"></div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Comments -->
                <?php if (comments_open() || get_comments_number()) : ?>
                <div class="card">
                    <div class="card-body">
                        <?php comments_template(); ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Right: Sidebar -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Metadata -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-lg font-display font-bold mb-4">Informations</h2>
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-dark-600 dark:text-dark-400 mb-1">Durée</dt>
                                <dd class="font-medium"><?php echo gmdate('i:s', $duration); ?></dd>
                            </div>
                            <div>
                                <dt class="text-dark-600 dark:text-dark-400 mb-1">Format</dt>
                                <dd class="font-medium uppercase"><?php echo $format; ?></dd>
                            </div>
                            <div>
                                <dt class="text-dark-600 dark:text-dark-400 mb-1">Taille</dt>
                                <dd class="font-medium"><?php echo size_format($filesize); ?></dd>
                            </div>
                            <?php if ($recorded_at) : ?>
                            <div>
                                <dt class="text-dark-600 dark:text-dark-400 mb-1">Date d'enregistrement</dt>
                                <dd class="font-medium"><?php echo date('j F Y', strtotime($recorded_at)); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($equipment) : ?>
                            <div>
                                <dt class="text-dark-600 dark:text-dark-400 mb-1">Équipement</dt>
                                <dd class="font-medium"><?php echo esc_html($equipment); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if ($license && !is_wp_error($license)) : ?>
                            <div>
                                <dt class="text-dark-600 dark:text-dark-400 mb-1">Licence</dt>
                                <dd class="font-medium"><?php echo $license[0]->name; ?></dd>
                            </div>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>

                <!-- Author Card -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-lg font-display font-bold mb-4">Contributeur</h2>
                        <a href="/profile/<?php echo get_the_author_meta('user_login'); ?>" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                            <?php echo get_avatar($author_id, 60, '', '', ['class' => 'rounded-full']); ?>
                            <div>
                                <div class="font-bold"><?php echo $author; ?></div>
                                <div class="text-sm text-dark-600 dark:text-dark-400">
                                    Voir le profil →
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Similar Sounds -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-lg font-display font-bold mb-4">Sons Similaires</h2>
                        <div id="similar-sounds" class="space-y-3">
                            <!-- Populated by JS from graph API -->
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</article>

<script>
// Sound data
const soundData = {
    id: <?php echo $sound_id; ?>,
    audioUrl: '<?php echo esc_js($audio_url); ?>',
    duration: <?php echo $duration ?: 0; ?>,
    isLiked: false, // Will be fetched if user is logged in
};

// Track play on load
if (typeof ArbAPI !== 'undefined') {
    ArbAPI.trackPlay(soundData.id);
}

// Initialize audio player (WaveSurfer will be loaded in player.js)
// This is a placeholder for the main player initialization
</script>

<?php
endwhile;
get_footer();
?>
