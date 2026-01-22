<?php
/**
 * REST API Endpoints for Upload
 */

if (!defined('ABSPATH'))
    exit;

class ARB_REST_Upload
{

    /**
     * Register REST routes
     */
    public static function register_routes()
    {
        // Presigned URL generation
        register_rest_route('arborisis/v1', '/upload/presign', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'presign'],
            'permission_callback' => [__CLASS__, 'can_upload'],
            'args' => [
                'filename' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_file_name',
                ],
                'content_type' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'filesize' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);

        // Finalize upload
        register_rest_route('arborisis/v1', '/upload/finalize', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'finalize'],
            'permission_callback' => [__CLASS__, 'can_upload'],
            'args' => [
                'key' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'sound_data' => [
                    'required' => true,
                    'type' => 'object',
                ],
            ],
        ]);
    }

    /**
     * Generate presigned URL
     */
    public static function presign($request)
    {
        $filename = $request->get_param('filename');
        $content_type = $request->get_param('content_type');
        $filesize = (int) $request->get_param('filesize');

        // Validate filesize
        $max_mb = (int) getenv('UPLOAD_MAX_MB') ?: 200;
        $max_bytes = $max_mb * 1024 * 1024;

        if ($filesize > $max_bytes) {
            return new WP_Error(
                'file_too_large',
                sprintf(__('Maximum file size is %d MB', 'arborisis-audio'), $max_mb),
                ['status' => 400]
            );
        }

        // Validate MIME type
        $allowed_mimes = explode(',', getenv('AUDIO_ALLOWED_MIMES') ?: 'audio/mpeg,audio/wav,audio/flac,audio/ogg');
        $allowed_mimes = array_map('trim', $allowed_mimes);

        if (!in_array($content_type, $allowed_mimes, true)) {
            return new WP_Error(
                'invalid_mime',
                __('Invalid audio format', 'arborisis-audio'),
                ['status' => 400]
            );
        }

        // Rate limiting
        $user_id = get_current_user_id();
        $last_upload = get_user_meta($user_id, '_arb_last_upload', true);
        $rate_limit = (int) getenv('RATE_LIMIT_PER_MINUTE') ?: 5;
        $min_interval = 60 / $rate_limit;

        if ($last_upload && (time() - $last_upload) < $min_interval) {
            return new WP_Error(
                'rate_limit',
                __('Upload too frequent. Please wait before uploading again.', 'arborisis-audio'),
                ['status' => 429]
            );
        }

        // Generate S3 key
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $prefix = getenv('S3_PREFIX') ?: 'sounds/';
        $key = $prefix . uniqid('sound_', true) . '.' . $ext;

        // Generate presigned URL
        try {
            $upload_url = ARB_S3_Client::create_presigned_put_url($key, $content_type);

            // Update rate limit timestamp
            update_user_meta($user_id, '_arb_last_upload', time());

            return new WP_REST_Response([
                'upload_url' => $upload_url,
                'upload_method' => 'PUT',
                'key' => $key,
                'expires_at' => time() + 900, // 15 minutes
            ], 200);

        } catch (Exception $e) {
            error_log('Presign error: ' . $e->getMessage());
            return new WP_Error(
                'presign_failed',
                __('Failed to generate upload URL', 'arborisis-audio'),
                ['status' => 500]
            );
        }
    }

    /**
     * Finalize upload and create sound post
     */
    public static function finalize($request)
    {
        $key = $request->get_param('key');
        $sound_data = $request->get_param('sound_data');

        // Verify S3 object exists
        if (!ARB_S3_Client::object_exists($key)) {
            return new WP_Error(
                'file_not_found',
                __('Uploaded file not found in storage', 'arborisis-audio'),
                ['status' => 404]
            );
        }

        // Create sound post
        $post_data = [
            'post_type' => 'sound',
            'post_title' => sanitize_text_field($sound_data['title'] ?? 'Untitled'),
            'post_content' => wp_kses_post($sound_data['content'] ?? ''),
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ];

        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        // Save audio metadata
        $public_url = ARB_S3_Client::get_public_url($key);
        update_post_meta($post_id, '_arb_audio_url', $public_url);
        update_post_meta($post_id, '_arb_audio_key', $key);

        if (isset($sound_data['duration'])) {
            update_post_meta($post_id, '_arb_duration', (float) $sound_data['duration']);
        }

        if (isset($sound_data['format'])) {
            update_post_meta($post_id, '_arb_format', sanitize_text_field($sound_data['format']));
        }

        if (isset($sound_data['filesize'])) {
            update_post_meta($post_id, '_arb_filesize', (int) $sound_data['filesize']);
        }

        // Save geo data with validation
        if (!empty($sound_data['geo'])) {
            $lat = (float) $sound_data['geo']['lat'];
            $lon = (float) $sound_data['geo']['lon'];

            // Validate GPS coordinates
            if ($lat < -90 || $lat > 90) {
                // Delete the post we just created since geo is invalid
                wp_delete_post($post_id, true);
                return new WP_Error(
                    'invalid_latitude',
                    __('Latitude must be between -90 and 90 degrees.', 'arborisis-audio'),
                    ['status' => 400]
                );
            }

            if ($lon < -180 || $lon > 180) {
                // Delete the post we just created since geo is invalid
                wp_delete_post($post_id, true);
                return new WP_Error(
                    'invalid_longitude',
                    __('Longitude must be between -180 and 180 degrees.', 'arborisis-audio'),
                    ['status' => 400]
                );
            }

            update_post_meta($post_id, '_arb_latitude', $lat);
            update_post_meta($post_id, '_arb_longitude', $lon);

            if (isset($sound_data['geo']['name'])) {
                update_post_meta($post_id, '_arb_location_name', sanitize_text_field($sound_data['geo']['name']));
            }

            // Trigger geo indexing if plugin is active
            if (function_exists('arb_geo_index_sound')) {
                arb_geo_index_sound($post_id, $lat, $lon);
            }
        }

        // Save recorded_at
        if (isset($sound_data['recorded_at'])) {
            update_post_meta($post_id, '_arb_recorded_at', sanitize_text_field($sound_data['recorded_at']));
        }

        // Save equipment
        if (isset($sound_data['equipment'])) {
            update_post_meta($post_id, '_arb_equipment', sanitize_text_field($sound_data['equipment']));
        }

        // Set tags
        if (!empty($sound_data['tags'])) {
            wp_set_object_terms($post_id, $sound_data['tags'], 'sound_tag');
        }

        // Set license
        if (!empty($sound_data['license'])) {
            wp_set_object_terms($post_id, [$sound_data['license']], 'sound_license');
        }

        // Initialize counts
        update_post_meta($post_id, '_arb_plays_count', 0);
        update_post_meta($post_id, '_arb_likes_count', 0);
        update_post_meta($post_id, '_arb_trending_score', 1.0);

        // Flush permalinks if this is one of the first sounds (to ensure rewrite rules are fresh)
        // This is a safety measure to ensure sound URLs work immediately
        $sound_count = wp_count_posts('sound');
        $total_sounds = $sound_count->publish + $sound_count->draft + $sound_count->pending;

        if ($total_sounds <= 1) {
            // First sound created - ensure permalinks are flushed
            flush_rewrite_rules(false);
        }

        return new WP_REST_Response([
            'success' => true,
            'sound_id' => $post_id,
            'sound_url' => get_permalink($post_id),
        ], 201);
    }

    /**
     * Permission callback
     */
    public static function can_upload()
    {
        return current_user_can('upload_sounds');
    }
}
