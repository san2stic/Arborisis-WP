<?php
/**
 * OpenSearch Indexer
 */

if (!defined('ABSPATH')) exit;

class ARB_Indexer {

    /**
     * Index a sound
     */
    public static function index_sound($post_id, $post, $update) {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if ($post->post_status !== 'publish') {
            self::delete_sound($post_id);
            return;
        }

        // Index synchronously
        self::index_sound_direct($post_id);
    }

    /**
     * Index sound directly
     */
    public static function index_sound_direct($post_id) {
        $client = ARB_OpenSearch_Client::get();

        if (!$client) {
            error_log("OpenSearch client not available for indexing sound {$post_id}");
            return false;
        }

        $doc = self::sound_to_doc($post_id);
        $index = ARB_OpenSearch_Client::get_index();

        try {
            $client->index([
                'index' => $index,
                'id'    => $post_id,
                'body'  => $doc,
            ]);
            return true;
        } catch (Exception $e) {
            error_log("OpenSearch index error for sound {$post_id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete sound from index
     */
    public static function delete_sound($post_id) {
        if (get_post_type($post_id) !== 'sound') {
            return;
        }

        $client = ARB_OpenSearch_Client::get();

        if (!$client) {
            return;
        }

        $index = ARB_OpenSearch_Client::get_index();

        try {
            $client->delete([
                'index' => $index,
                'id'    => $post_id,
            ]);
        } catch (Exception $e) {
            // Ignore 404 errors (document doesn't exist)
            if (strpos($e->getMessage(), '404') === false) {
                error_log("OpenSearch delete error for sound {$post_id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Convert sound post to OpenSearch document
     */
    public static function sound_to_doc($post_id) {
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'sound') {
            return null;
        }

        $tags = wp_get_object_terms($post_id, 'sound_tag', ['fields' => 'names']);
        $license = wp_get_object_terms($post_id, 'sound_license', ['fields' => 'names']);

        $lat = get_post_meta($post_id, '_arb_latitude', true);
        $lon = get_post_meta($post_id, '_arb_longitude', true);

        $doc = [
            'id'             => $post_id,
            'title'          => $post->post_title,
            'content'        => wp_strip_all_tags($post->post_content),
            'tags'           => is_array($tags) ? $tags : [],
            'author_id'      => $post->post_author,
            'author_name'    => get_the_author_meta('display_name', $post->post_author),
            'license'        => !empty($license) ? $license[0] : null,
            'duration'       => (float) get_post_meta($post_id, '_arb_duration', true),
            'plays'          => (int) get_post_meta($post_id, '_arb_plays_count', true),
            'likes'          => (int) get_post_meta($post_id, '_arb_likes_count', true),
            'trending_score' => (float) get_post_meta($post_id, '_arb_trending_score', true) ?: 1.0,
            'created_at'     => $post->post_date,
            'updated_at'     => $post->post_modified,
        ];

        // Add location if available
        if ($lat && $lon) {
            $doc['location'] = [
                'lat' => (float) $lat,
                'lon' => (float) $lon,
            ];
            $doc['location_name'] = get_post_meta($post_id, '_arb_location_name', true);
        }

        return $doc;
    }

    /**
     * Bulk index sounds
     */
    public static function bulk_index($sound_ids) {
        $client = ARB_OpenSearch_Client::get();

        if (!$client) {
            return false;
        }

        $index = ARB_OpenSearch_Client::get_index();
        $params = ['body' => []];

        foreach ($sound_ids as $sound_id) {
            $doc = self::sound_to_doc($sound_id);

            if (!$doc) continue;

            $params['body'][] = [
                'index' => [
                    '_index' => $index,
                    '_id'    => $sound_id,
                ],
            ];
            $params['body'][] = $doc;
        }

        if (empty($params['body'])) {
            return false;
        }

        try {
            $response = $client->bulk($params);
            return !isset($response['errors']) || !$response['errors'];
        } catch (Exception $e) {
            error_log("OpenSearch bulk index error: " . $e->getMessage());
            return false;
        }
    }
}
