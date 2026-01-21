<?php
/**
 * REST API Endpoints for Sounds
 */

if (!defined('ABSPATH')) exit;

class ARB_REST_Sounds {

    /**
     * Register REST routes
     */
    public static function register_routes() {
        // List and create sounds
        register_rest_route('arborisis/v1', '/sounds', [
            [
                'methods'             => 'GET',
                'callback'            => [__CLASS__, 'list_sounds'],
                'permission_callback' => '__return_true',
                'args'                => self::get_collection_params(),
            ],
            [
                'methods'             => 'POST',
                'callback'            => [__CLASS__, 'create_sound'],
                'permission_callback' => [__CLASS__, 'can_upload_sounds'],
            ],
        ]);

        // Single sound operations
        register_rest_route('arborisis/v1', '/sounds/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [__CLASS__, 'get_sound'],
                'permission_callback' => '__return_true',
            ],
            [
                'methods'             => 'PUT',
                'callback'            => [__CLASS__, 'update_sound'],
                'permission_callback' => [__CLASS__, 'can_edit_sound'],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [__CLASS__, 'delete_sound'],
                'permission_callback' => [__CLASS__, 'can_delete_sound'],
            ],
        ]);
    }

    /**
     * List sounds with filters
     */
    public static function list_sounds($request) {
        $page     = (int) $request->get_param('page') ?: 1;
        $per_page = min((int) $request->get_param('per_page') ?: 20, 100);
        $tags     = $request->get_param('tags');
        $orderby  = $request->get_param('orderby') ?: 'recent';
        $q        = $request->get_param('q');

        $args = [
            'post_type'      => 'sound',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $page,
        ];

        // Text search
        if ($q) {
            $args['s'] = sanitize_text_field($q);
        }

        // Tag filter
        if ($tags) {
            $tag_slugs = array_map('trim', explode(',', $tags));
            $args['tax_query'] = [[
                'taxonomy' => 'sound_tag',
                'field'    => 'slug',
                'terms'    => $tag_slugs,
            ]];
        }

        // Order by
        switch ($orderby) {
            case 'popular':
                $args['meta_key'] = '_arb_plays_count';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            case 'trending':
                $args['meta_key'] = '_arb_trending_score';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'DESC';
                break;
            default: // recent
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
        }

        $query = new WP_Query($args);
        $sounds = array_map([__CLASS__, 'format_sound'], $query->posts);

        return new WP_REST_Response([
            'sounds' => $sounds,
            'total'  => $query->found_posts,
            'pages'  => $query->max_num_pages,
        ], 200);
    }

    /**
     * Get single sound
     */
    public static function get_sound($request) {
        $id = (int) $request['id'];
        $sound = get_post($id);

        if (!$sound || $sound->post_type !== 'sound') {
            return new WP_Error('not_found', __('Sound not found', 'arborisis-core'), ['status' => 404]);
        }

        return new WP_REST_Response(self::format_sound_detail($sound), 200);
    }

    /**
     * Create sound (handled by upload/finalize in arborisis-audio)
     */
    public static function create_sound($request) {
        return new WP_Error('not_implemented', __('Use /upload/finalize endpoint', 'arborisis-core'), ['status' => 501]);
    }

    /**
     * Update sound
     */
    public static function update_sound($request) {
        $id = (int) $request['id'];
        $data = $request->get_json_params();

        $post_data = [
            'ID' => $id,
        ];

        if (isset($data['title'])) {
            $post_data['post_title'] = sanitize_text_field($data['title']);
        }

        if (isset($data['content'])) {
            $post_data['post_content'] = wp_kses_post($data['content']);
        }

        $updated = wp_update_post($post_data, true);

        if (is_wp_error($updated)) {
            return $updated;
        }

        // Update tags
        if (isset($data['tags'])) {
            wp_set_object_terms($id, $data['tags'], 'sound_tag');
        }

        // Update license
        if (isset($data['license'])) {
            wp_set_object_terms($id, [$data['license']], 'sound_license');
        }

        // Update geo
        if (isset($data['geo'])) {
            update_post_meta($id, '_arb_latitude', (float) $data['geo']['lat']);
            update_post_meta($id, '_arb_longitude', (float) $data['geo']['lon']);
            if (isset($data['geo']['name'])) {
                update_post_meta($id, '_arb_location_name', sanitize_text_field($data['geo']['name']));
            }
        }

        // Update recorded_at
        if (isset($data['recorded_at'])) {
            update_post_meta($id, '_arb_recorded_at', sanitize_text_field($data['recorded_at']));
        }

        // Update equipment
        if (isset($data['equipment'])) {
            update_post_meta($id, '_arb_equipment', sanitize_text_field($data['equipment']));
        }

        return self::get_sound($request);
    }

    /**
     * Delete sound
     */
    public static function delete_sound($request) {
        $id = (int) $request['id'];
        $deleted = wp_delete_post($id, true);

        if (!$deleted) {
            return new WP_Error('delete_failed', __('Failed to delete sound', 'arborisis-core'), ['status' => 500]);
        }

        return new WP_REST_Response(['success' => true], 200);
    }

    /**
     * Format sound for list response
     */
    private static function format_sound($post) {
        $tags = wp_get_object_terms($post->ID, 'sound_tag', ['fields' => 'names']);
        $lat = get_post_meta($post->ID, '_arb_latitude', true);
        $lon = get_post_meta($post->ID, '_arb_longitude', true);

        return [
            'id'         => $post->ID,
            'title'      => $post->post_title,
            'author'     => get_the_author_meta('display_name', $post->post_author),
            'author_id'  => $post->post_author,
            'audio_url'  => get_post_meta($post->ID, '_arb_audio_url', true),
            'duration'   => (float) get_post_meta($post->ID, '_arb_duration', true),
            'tags'       => $tags,
            'geo'        => ($lat && $lon) ? [
                'lat' => (float) $lat,
                'lon' => (float) $lon,
            ] : null,
            'plays'      => (int) get_post_meta($post->ID, '_arb_plays_count', true),
            'likes'      => (int) get_post_meta($post->ID, '_arb_likes_count', true),
            'created_at' => $post->post_date,
        ];
    }

    /**
     * Format sound detail for single response
     */
    private static function format_sound_detail($post) {
        $basic = self::format_sound($post);
        $license = wp_get_object_terms($post->ID, 'sound_license', ['fields' => 'names']);

        return array_merge($basic, [
            'content'        => $post->post_content,
            'format'         => get_post_meta($post->ID, '_arb_format', true),
            'filesize'       => (int) get_post_meta($post->ID, '_arb_filesize', true),
            'license'        => !empty($license) ? $license[0] : null,
            'location_name'  => get_post_meta($post->ID, '_arb_location_name', true),
            'recorded_at'    => get_post_meta($post->ID, '_arb_recorded_at', true),
            'equipment'      => get_post_meta($post->ID, '_arb_equipment', true),
            'waveform_data'  => json_decode(get_post_meta($post->ID, '_arb_waveform_data', true), true),
            'comments_count' => get_comments_number($post->ID),
            'user_has_liked' => self::user_has_liked($post->ID),
        ]);
    }

    /**
     * Check if current user has liked the sound
     */
    private static function user_has_liked($sound_id) {
        if (!is_user_logged_in()) {
            return false;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'arb_likes';
        $user_id = get_current_user_id();

        $liked = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table} WHERE user_id = %d AND sound_id = %d",
            $user_id,
            $sound_id
        ));

        return (bool) $liked;
    }

    /**
     * Permission callbacks
     */
    public static function can_upload_sounds() {
        return current_user_can('upload_sounds');
    }

    public static function can_edit_sound($request) {
        $id = (int) $request['id'];
        return current_user_can('edit_sound', $id);
    }

    public static function can_delete_sound($request) {
        $id = (int) $request['id'];
        return current_user_can('delete_sound', $id);
    }

    /**
     * Collection parameters
     */
    private static function get_collection_params() {
        return [
            'page' => [
                'description' => __('Current page of the collection.', 'arborisis-core'),
                'type'        => 'integer',
                'default'     => 1,
                'minimum'     => 1,
            ],
            'per_page' => [
                'description' => __('Maximum number of items to return.', 'arborisis-core'),
                'type'        => 'integer',
                'default'     => 20,
                'minimum'     => 1,
                'maximum'     => 100,
            ],
            'tags' => [
                'description' => __('Filter by tag slugs (comma-separated).', 'arborisis-core'),
                'type'        => 'string',
            ],
            'q' => [
                'description' => __('Search query.', 'arborisis-core'),
                'type'        => 'string',
            ],
            'orderby' => [
                'description' => __('Sort order.', 'arborisis-core'),
                'type'        => 'string',
                'default'     => 'recent',
                'enum'        => ['recent', 'popular', 'trending'],
            ],
        ];
    }
}
