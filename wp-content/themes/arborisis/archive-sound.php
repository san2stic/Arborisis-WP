<?php
/**
 * Archive template for sound post type
 */

get_header();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom">

        <!-- Archive Header -->
        <div class="mb-8">
            <?php if (is_tax('sound_tag')) : ?>
                <div class="flex items-center gap-2 text-sm text-dark-600 dark:text-dark-400 mb-4">
                    <a href="/" class="hover:text-primary-600">Accueil</a>
                    <span>/</span>
                    <a href="/explore" class="hover:text-primary-600">Explorer</a>
                    <span>/</span>
                    <span class="text-dark-900 dark:text-dark-50">Tag: <?php single_tag_title(); ?></span>
                </div>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                    <span class="badge badge-primary text-2xl mr-3">#</span>
                    <?php single_tag_title(); ?>
                </h1>
                <?php if (tag_description()) : ?>
                    <p class="text-lg text-dark-600 dark:text-dark-400 max-w-2xl">
                        <?php echo tag_description(); ?>
                    </p>
                <?php endif; ?>
            <?php elseif (is_tax('sound_license')) : ?>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                    Licence: <?php single_tag_title(); ?>
                </h1>
            <?php else : ?>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                    Tous les Enregistrements
                </h1>
            <?php endif; ?>

            <div class="text-sm text-dark-600 dark:text-dark-400 mt-4">
                <?php
                global $wp_query;
                echo $wp_query->found_posts . ' enregistrement' . ($wp_query->found_posts > 1 ? 's' : '') . ' trouvé' . ($wp_query->found_posts > 1 ? 's' : '');
                ?>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex gap-2">
                <a href="?orderby=recent" class="btn btn-sm <?php echo (!isset($_GET['orderby']) || $_GET['orderby'] === 'recent') ? 'btn-primary' : 'btn-ghost'; ?>">
                    Plus récents
                </a>
                <a href="?orderby=popular" class="btn btn-sm <?php echo (isset($_GET['orderby']) && $_GET['orderby'] === 'popular') ? 'btn-primary' : 'btn-ghost'; ?>">
                    Plus populaires
                </a>
                <a href="?orderby=trending" class="btn btn-sm <?php echo (isset($_GET['orderby']) && $_GET['orderby'] === 'trending') ? 'btn-primary' : 'btn-ghost'; ?>">
                    Tendances
                </a>
            </div>
        </div>

        <?php if (have_posts()) : ?>

            <!-- Sounds Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-12">
                <?php while (have_posts()) : the_post();
                    $sound_id = get_the_ID();
                    $audio_url = get_post_meta($sound_id, '_arb_audio_url', true);
                    $duration = get_post_meta($sound_id, '_arb_duration', true);
                    $plays_count = get_post_meta($sound_id, '_arb_plays_count', true) ?: 0;
                    $likes_count = get_post_meta($sound_id, '_arb_likes_count', true) ?: 0;
                    $tags = get_the_terms($sound_id, 'sound_tag');
                ?>

                    <a href="<?php the_permalink(); ?>" class="sound-card">
                        <div class="sound-card-image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('sound-thumbnail', ['class' => 'w-full h-full object-cover', 'loading' => 'lazy']); ?>
                            <?php else : ?>
                                <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white text-4xl font-bold">
                                    <?php echo strtoupper(substr(get_the_title(), 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div class="sound-card-play-button">
                                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-xl">
                                    <svg class="w-8 h-8 text-primary-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="font-bold text-lg mb-2 line-clamp-1"><?php the_title(); ?></h3>
                            <p class="text-sm text-dark-600 dark:text-dark-400 mb-3 line-clamp-2">
                                <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                            </p>
                            <div class="flex items-center justify-between text-xs text-dark-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?php echo gmdate('i:s', $duration); ?>
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    </svg>
                                    <?php echo number_format($plays_count); ?>
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                    <?php echo number_format($likes_count); ?>
                                </span>
                            </div>
                            <?php if ($tags && !is_wp_error($tags)) : ?>
                                <div class="flex flex-wrap gap-1 mt-3">
                                    <?php foreach (array_slice($tags, 0, 3) as $tag) : ?>
                                        <span class="badge badge-primary"><?php echo $tag->name; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>

                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-center gap-2">
                <?php
                echo paginate_links([
                    'prev_text' => '← Précédent',
                    'next_text' => 'Suivant →',
                    'type' => 'list',
                    'class' => 'btn btn-ghost btn-sm',
                ]);
                ?>
            </div>

        <?php else : ?>

            <!-- No Results -->
            <div class="text-center py-20">
                <svg class="w-24 h-24 mx-auto mb-6 text-dark-300 dark:text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h2 class="text-2xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                    Aucun enregistrement trouvé
                </h2>
                <p class="text-dark-600 dark:text-dark-400 mb-8">
                    <?php if (is_tax()) : ?>
                        Il n'y a pas encore d'enregistrements pour ce tag.
                    <?php else : ?>
                        Il n'y a pas encore d'enregistrements disponibles.
                    <?php endif; ?>
                </p>
                <a href="/explore" class="btn btn-primary">
                    Parcourir tous les sons
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
