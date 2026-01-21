<?php
/**
 * Comments template
 */

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="text-2xl font-display font-bold mb-6">
            <?php
            $comment_count = get_comments_number();
            if ($comment_count === 1) {
                echo '1 Commentaire';
            } else {
                echo $comment_count . ' Commentaires';
            }
            ?>
        </h2>

        <ol class="comment-list space-y-6 mb-8">
            <?php
            wp_list_comments([
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
                'callback' => 'arborisis_comment_callback',
            ]);
            ?>
        </ol>

        <?php
        the_comments_navigation([
            'prev_text' => '← Commentaires précédents',
            'next_text' => 'Commentaires suivants →',
        ]);
        ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments text-dark-600 dark:text-dark-400">
            Les commentaires sont fermés.
        </p>
    <?php endif; ?>

    <?php
    comment_form([
        'title_reply' => 'Laisser un commentaire',
        'title_reply_to' => 'Répondre à %s',
        'title_reply_before' => '<h3 class="text-2xl font-display font-bold mb-6">',
        'title_reply_after' => '</h3>',
        'comment_notes_before' => '',
        'comment_notes_after' => '',
        'class_form' => 'space-y-4',
        'class_submit' => 'btn btn-primary',
        'submit_button' => '<button type="submit" name="submit" class="btn btn-primary">Publier le commentaire</button>',
        'fields' => [
            'author' => '<div class="comment-form-author">
                <label for="author" class="block text-sm font-medium mb-2">Nom *</label>
                <input id="author" name="author" type="text" class="input" required />
            </div>',
            'email' => '<div class="comment-form-email">
                <label for="email" class="block text-sm font-medium mb-2">Email *</label>
                <input id="email" name="email" type="email" class="input" required />
            </div>',
            'url' => '<div class="comment-form-url">
                <label for="url" class="block text-sm font-medium mb-2">Site web</label>
                <input id="url" name="url" type="url" class="input" />
            </div>',
        ],
        'comment_field' => '<div class="comment-form-comment">
            <label for="comment" class="block text-sm font-medium mb-2">Commentaire *</label>
            <textarea id="comment" name="comment" rows="6" class="input" required></textarea>
        </div>',
    ]);
    ?>

</div>

<?php
/**
 * Custom comment callback
 */
function arborisis_comment_callback($comment, $args, $depth) {
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class('flex gap-4 p-4 bg-dark-50 dark:bg-dark-800 rounded-lg'); ?>>
        <div class="flex-shrink-0">
            <?php echo get_avatar($comment, 48, '', '', ['class' => 'rounded-full']); ?>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-2">
                <div class="font-medium text-dark-900 dark:text-dark-50">
                    <?php comment_author_link(); ?>
                </div>
                <div class="text-xs text-dark-500">
                    <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>" class="hover:text-primary-600">
                        <?php comment_date(); ?> à <?php comment_time(); ?>
                    </a>
                </div>
            </div>

            <?php if ('0' == $comment->comment_approved) : ?>
                <p class="text-sm text-yellow-600 dark:text-yellow-400 mb-2">
                    Votre commentaire est en attente de modération.
                </p>
            <?php endif; ?>

            <div class="text-dark-700 dark:text-dark-300 mb-2">
                <?php comment_text(); ?>
            </div>

            <?php
            comment_reply_link(array_merge($args, [
                'depth' => $depth,
                'max_depth' => $args['max_depth'],
                'reply_text' => '<svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg> Répondre',
                'before' => '<div class="reply text-xs">',
                'after' => '</div>',
            ]));
            ?>
        </div>
    </<?php echo $tag; ?>>
    <?php
}
?>
