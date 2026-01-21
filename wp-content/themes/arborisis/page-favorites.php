<?php
/**
 * Template Name: Favorites
 * Description: Sons likés par l'utilisateur
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(home_url('/favorites')));
    exit;
}

get_header();

$current_user = wp_get_current_user();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom">

        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Mes Favoris
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400">
                Les enregistrements que vous avez likés
            </p>
        </div>

        <div id="favorites-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            global $wpdb;
            $liked_ids = $wpdb->get_col($wpdb->prepare(
                "SELECT sound_id FROM {$wpdb->prefix}arb_likes WHERE user_id = %d ORDER BY created_at DESC",
                $current_user->ID
            ));

            if (!empty($liked_ids)) :
                $args = [
                    'post_type' => 'sound',
                    'post__in' => $liked_ids,
                    'orderby' => 'post__in',
                    'posts_per_page' => -1,
                ];
                $query = new WP_Query($args);

                if ($query->have_posts()) :
                    while ($query->have_posts()) : $query->the_post();
                        $sound_id = get_the_ID();
                        $author_id = get_post_field('post_author', $sound_id);
                        $author = get_userdata($author_id);
                        $plays_count = get_post_meta($sound_id, '_arb_plays_count', true) ?: 0;
                        $likes_count = get_post_meta($sound_id, '_arb_likes_count', true) ?: 0;
                        $thumbnail = get_the_post_thumbnail_url($sound_id, 'medium') ?: get_template_directory_uri() . '/assets/placeholder.jpg';
            ?>

                        <div class="card">
                            <a href="<?php the_permalink(); ?>" class="block">
                                <img src="<?php echo esc_url($thumbnail); ?>"
                                     alt="<?php the_title(); ?>"
                                     class="w-full h-48 object-cover rounded-t-lg">
                            </a>
                            <div class="card-body">
                                <h3 class="font-bold text-lg mb-2">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-primary-600">
                                        <?php the_title(); ?>
                                    </a>
                                </h3>

                                <p class="text-sm text-dark-600 dark:text-dark-400 mb-4">
                                    Par <a href="/profile/<?php echo esc_attr($author->user_login); ?>" class="hover:text-primary-600">
                                        <?php echo esc_html($author->display_name); ?>
                                    </a>
                                </p>

                                <div class="flex items-center justify-between text-sm text-dark-600 dark:text-dark-400 mb-4">
                                    <span><?php echo number_format($plays_count); ?> plays</span>
                                    <span><?php echo number_format($likes_count); ?> likes</span>
                                </div>

                                <div class="flex gap-2">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-secondary flex-1">
                                        Écouter
                                    </a>
                                    <button
                                        onclick="unlikeSound(<?php echo $sound_id; ?>)"
                                        class="btn btn-sm bg-red-600 hover:bg-red-700 text-white">
                                        ❤️ Unlike
                                    </button>
                                </div>
                            </div>
                        </div>

            <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
            else :
            ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-dark-500 text-lg mb-4">Vous n'avez pas encore liké d'enregistrements.</p>
                    <a href="<?php echo home_url('/explore'); ?>" class="btn btn-primary">
                        Explorer les sons
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
async function unlikeSound(soundId) {
    try {
        const response = await fetch(`/wp-json/arborisis/v1/sounds/${soundId}/like`, {
            method: 'POST',
            headers: {
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            }
        });

        if (response.ok) {
            location.reload();
        } else {
            throw new Error('Échec unlike');
        }
    } catch (error) {
        console.error('Unlike error:', error);
        alert('Erreur. Veuillez réessayer.');
    }
}
</script>

<?php get_footer(); ?>
