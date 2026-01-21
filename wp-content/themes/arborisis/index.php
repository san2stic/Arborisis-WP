<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @package Arborisis
 */

get_header();
?>

<main class="min-h-screen bg-dark-50 dark:bg-dark-950 py-20">
    <div class="container-custom">
        
        <?php if (have_posts()) : ?>

            <header class="mb-12">
                <?php if (is_home() && !is_front_page()) : ?>
                    <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                        <?php single_post_title(); ?>
                    </h1>
                <?php else : ?>
                    <h1 class="text-4xl md:text-5xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                        📝 Blog
                    </h1>
                <?php endif; ?>
                
                <?php
                $description = get_the_archive_description();
                if ($description) : ?>
                    <div class="text-lg text-dark-600 dark:text-dark-400 max-w-3xl">
                        <?php echo wp_kses_post(wpautop($description)); ?>
                    </div>
                <?php endif; ?>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                /* Start the Loop */
                while (have_posts()) :
                    the_post();
                    ?>
                    
                    <article id="post-<?php the_ID(); ?>" <?php post_class('card group'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="block">
                                <div class="aspect-video overflow-hidden">
                                    <?php the_post_thumbnail('large', [
                                        'class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-300',
                                        'loading' => 'lazy'
                                    ]); ?>
                                </div>
                            </a>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <header class="mb-4">
                                <?php
                                the_title(
                                    '<h2 class="text-xl font-display font-bold mb-2"><a href="' . esc_url(get_permalink()) . '" class="hover:text-primary-600 dark:hover:text-primary-500 transition-colors">',
                                    '</a></h2>'
                                );
                                ?>
                                
                                <div class="flex items-center gap-4 text-sm text-dark-500 dark:text-dark-400">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo get_the_date(); ?>
                                    </time>
                                    <?php if (get_the_author()) : ?>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <?php the_author(); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </header>

                            <div class="text-dark-600 dark:text-dark-400 mb-4">
                                <?php the_excerpt(); ?>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">
                                Lire la suite
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>

                            <?php if (has_category() || has_tag()) : ?>
                                <footer class="mt-4 pt-4 border-t border-dark-200 dark:border-dark-700">
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories) {
                                        echo '<div class="flex flex-wrap gap-2 mb-2">';
                                        foreach ($categories as $category) {
                                            echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="badge badge-secondary">' . esc_html($category->name) . '</a>';
                                        }
                                        echo '</div>';
                                    }

                                    $tags = get_the_tags();
                                    if ($tags) {
                                        echo '<div class="flex flex-wrap gap-2">';
                                        foreach ($tags as $tag) {
                                            echo '<a href="' . esc_url(get_tag_link($tag->term_id)) . '" class="badge badge-primary">' . esc_html($tag->name) . '</a>';
                                        }
                                        echo '</div>';
                                    }
                                    ?>
                                </footer>
                            <?php endif; ?>
                        </div>
                    </article>

                <?php endwhile; ?>
            </div>

            <?php
            // Pagination
            the_posts_pagination([
                'mid_size'  => 2,
                'prev_text' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Précédent',
                'next_text' => 'Suivant <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
                'class'     => 'mt-12',
            ]);
            ?>

        <?php else : ?>

            <div class="text-center py-20">
                <div class="max-w-2xl mx-auto">
                    <svg class="w-24 h-24 mx-auto text-dark-300 dark:text-dark-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    
                    <h1 class="text-3xl font-display font-bold text-dark-900 dark:text-dark-50 mb-4">
                        Aucun contenu trouvé
                    </h1>
                    
                    <p class="text-lg text-dark-600 dark:text-dark-400 mb-8">
                        Il semble qu'il n'y ait rien ici pour le moment.
                    </p>

                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Retour à l'accueil
                        </a>
                        
                        <a href="<?php echo esc_url(home_url('/explore')); ?>" class="btn btn-outline">
                            Explorer les sons
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php
get_footer();
