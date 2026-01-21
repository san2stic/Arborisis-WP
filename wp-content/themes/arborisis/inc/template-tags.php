<?php
/**
 * Custom template tags for this theme
 *
 * @package Arborisis
 */

if (!defined('ABSPATH'))
    exit;

/**
 * Prints HTML with meta information for the current post-date/time
 */
function arborisis_posted_on()
{
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date()),
        esc_attr(get_the_modified_date(DATE_W3C)),
        esc_html(get_the_modified_date())
    );

    $posted_on = sprintf(
        /* translators: %s: post date */
        esc_html_x('Publié le %s', 'post date', 'arborisis'),
        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span>';
}

/**
 * Prints HTML with meta information for the current author
 */
function arborisis_posted_by()
{
    $byline = sprintf(
        /* translators: %s: post author */
        esc_html_x('par %s', 'post author', 'arborisis'),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
    );

    echo '<span class="byline"> ' . $byline . '</span>';
}

/**
 * Prints HTML with meta information for categories, tags and comments
 */
function arborisis_entry_footer()
{
    // Hide category and tag text for pages
    if ('post' === get_post_type()) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list(esc_html__(', ', 'arborisis'));
        if ($categories_list) {
            /* translators: 1: list of categories */
            printf('<span class="cat-links">' . esc_html__('Publié dans %1$s', 'arborisis') . '</span>', $categories_list);
        }

        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'arborisis'));
        if ($tags_list) {
            /* translators: 1: list of tags */
            printf('<span class="tags-links">' . esc_html__('Taggé %1$s', 'arborisis') . '</span>', $tags_list);
        }
    }

    if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
        echo '<span class="comments-link">';
        comments_popup_link(
            sprintf(
                wp_kses(
                    /* translators: %s: post title */
                    __('Laisser un commentaire<span class="screen-reader-text"> sur %s</span>', 'arborisis'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            )
        );
        echo '</span>';
    }

    edit_post_link(
        sprintf(
            wp_kses(
                /* translators: %s: post title */
                __('Modifier <span class="screen-reader-text">%s</span>', 'arborisis'),
                array(
                    'span' => array(
                        'class' => array(),
                    ),
                )
            ),
            wp_kses_post(get_the_title())
        ),
        '<span class="edit-link">',
        '</span>'
    );
}

/**
 * Displays an optional post thumbnail
 */
function arborisis_post_thumbnail()
{
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()):
        ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('large', ['class' => 'w-full h-auto rounded-lg']); ?>
        </div>
    <?php else: ?>
        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
            the_post_thumbnail('post-thumbnail', [
                'alt' => the_title_attribute([
                    'echo' => false,
                ]),
            ]);
            ?>
        </a>
        <?php
    endif;
}

/**
 * Display sound duration formatted
 */
function arborisis_format_duration($seconds)
{
    if (!$seconds)
        return '0:00';

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = floor($seconds % 60);

    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
    }

    return sprintf('%d:%02d', $minutes, $secs);
}

/**
 * Display file size formatted
 */
function arborisis_format_filesize($bytes)
{
    if (!$bytes)
        return '0 B';

    $units = ['B', 'KB', 'MB', 'GB'];
    $power = floor(log($bytes, 1024));
    $power = min($power, count($units) - 1);

    return sprintf('%.1f %s', $bytes / pow(1024, $power), $units[$power]);
}

/**
 * Get sound license label
 */
function arborisis_get_license_label($license)
{
    $licenses = [
        'cc0' => 'CC0 - Public Domain',
        'cc-by' => 'CC BY - Attribution',
        'cc-by-sa' => 'CC BY-SA - Attribution ShareAlike',
        'cc-by-nc' => 'CC BY-NC - Attribution NonCommercial',
        'cc-by-nc-sa' => 'CC BY-NC-SA - Attribution NonCommercial ShareAlike',
    ];

    return $licenses[$license] ?? $license;
}

/**
 * Get user avatar URL
 */
function arborisis_get_avatar_url($user_id, $size = 96)
{
    return get_avatar_url($user_id, ['size' => $size]);
}

/**
 * Check if user can edit sound
 */
function arborisis_can_edit_sound($sound_id)
{
    if (!is_user_logged_in()) {
        return false;
    }

    $sound = get_post($sound_id);
    if (!$sound) {
        return false;
    }

    return current_user_can('edit_post', $sound_id) || get_current_user_id() === (int) $sound->post_author;
}

/**
 * Get sound tags as array
 */
function arborisis_get_sound_tags($sound_id)
{
    $tags = get_the_terms($sound_id, 'sound_tag');
    if (is_wp_error($tags) || !$tags) {
        return [];
    }

    return array_map(function ($tag) {
        return $tag->name;
    }, $tags);
}

/**
 * Format number with K/M notation
 */
function arborisis_format_number($num)
{
    if ($num >= 1000000) {
        return round($num / 1000000, 1) . 'M';
    }
    if ($num >= 1000) {
        return round($num / 1000, 1) . 'K';
    }
    return (string) $num;
}
