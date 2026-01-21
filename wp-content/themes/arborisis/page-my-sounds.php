<?php
/**
 * Template Name: My Sounds
 * Description: Gestion des enregistrements de l'utilisateur connecté
 */

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(home_url('/my-sounds')));
    exit;
}

get_header();

$current_user = wp_get_current_user();
?>

<div class="py-8 bg-white dark:bg-dark-900 min-h-screen">
    <div class="container-custom">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                Mes Enregistrements
            </h1>
            <p class="text-lg text-dark-600 dark:text-dark-400">
                Gérez vos sons, consultez les statistiques et éditez les métadonnées
            </p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            <?php
            $args = [
                'post_type' => 'sound',
                'author' => $current_user->ID,
                'posts_per_page' => -1,
                'fields' => 'ids',
            ];
            $my_sounds = new WP_Query($args);
            $total_sounds = $my_sounds->found_posts;

            $total_plays = get_user_meta($current_user->ID, '_arb_total_plays', true) ?: 0;
            $total_likes = get_user_meta($current_user->ID, '_arb_total_likes', true) ?: 0;
            ?>

            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400"><?php echo $total_sounds; ?></div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Enregistrements
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400"><?php echo number_format($total_plays); ?></div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Écoutes totales
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400"><?php echo number_format($total_likes); ?></div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Likes totaux
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <div class="text-4xl font-bold text-primary-600 dark:text-primary-400">
                        <?php echo $total_sounds > 0 ? number_format($total_plays / $total_sounds, 1) : '0'; ?>
                    </div>
                    <div class="text-sm uppercase tracking-wide text-dark-600 dark:text-dark-400 mt-2">
                        Écoutes / son
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex gap-4">
                <select id="filter-sounds" class="input">
                    <option value="all">Tous les sons</option>
                    <option value="recent">Plus récents</option>
                    <option value="popular">Plus populaires</option>
                    <option value="unpublished">Brouillons</option>
                </select>
            </div>

            <a href="<?php echo home_url('/upload'); ?>" class="btn btn-primary">
                + Uploader un nouveau son
            </a>
        </div>

        <!-- Sounds Grid -->
        <div id="my-sounds-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $args = [
                'post_type' => 'sound',
                'author' => $current_user->ID,
                'posts_per_page' => 20,
                'orderby' => 'date',
                'order' => 'DESC',
            ];
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    $sound_id = get_the_ID();
                    $audio_url = get_post_meta($sound_id, '_arb_audio_url', true);
                    $duration = get_post_meta($sound_id, '_arb_duration', true);
                    $plays_count = get_post_meta($sound_id, '_arb_plays_count', true) ?: 0;
                    $likes_count = get_post_meta($sound_id, '_arb_likes_count', true) ?: 0;
                    $thumbnail = get_the_post_thumbnail_url($sound_id, 'medium') ?: get_template_directory_uri() . '/assets/placeholder.jpg';
            ?>

                    <div class="card group">
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

                            <div class="flex items-center justify-between text-sm text-dark-600 dark:text-dark-400 mb-4">
                                <span><?php echo number_format($plays_count); ?> plays</span>
                                <span><?php echo number_format($likes_count); ?> likes</span>
                                <span><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?></span>
                            </div>

                            <div class="flex gap-2">
                                <a href="<?php the_permalink(); ?>" class="btn btn-sm btn-secondary flex-1">
                                    Voir
                                </a>
                                <button
                                    onclick="editSound(<?php echo $sound_id; ?>)"
                                    class="btn btn-sm btn-primary">
                                    Éditer
                                </button>
                                <button
                                    onclick="deleteSound(<?php echo $sound_id; ?>)"
                                    class="btn btn-sm bg-red-600 hover:bg-red-700 text-white">
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>

            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-dark-500 text-lg mb-4">Vous n'avez pas encore uploadé d'enregistrements.</p>
                    <a href="<?php echo home_url('/upload'); ?>" class="btn btn-primary">
                        Uploader votre premier son
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function editSound(soundId) {
    // Redirect to WordPress admin edit page
    window.location.href = '/wp-admin/post.php?post=' + soundId + '&action=edit';
}

async function deleteSound(soundId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement ? Cette action est irréversible.')) {
        return;
    }

    try {
        const response = await fetch(`/wp-json/arborisis/v1/sounds/${soundId}`, {
            method: 'DELETE',
            headers: {
                'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
            }
        });

        if (response.ok) {
            alert('Enregistrement supprimé avec succès.');
            location.reload();
        } else {
            throw new Error('Échec de la suppression');
        }
    } catch (error) {
        console.error('Delete error:', error);
        alert('Erreur lors de la suppression. Veuillez réessayer.');
    }
}

// Filter sounds
document.getElementById('filter-sounds').addEventListener('change', (e) => {
    const filter = e.target.value;
    const url = new URL(window.location);
    url.searchParams.set('filter', filter);
    window.location.href = url;
});
</script>

<?php get_footer(); ?>
