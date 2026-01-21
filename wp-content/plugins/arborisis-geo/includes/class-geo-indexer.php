<?php
/**
 * Geo Indexer
 */

if (!defined('ABSPATH')) exit;

class ARB_Geo_Indexer {

    /**
     * Index sound geo data
     */
    public static function index_sound($sound_id) {
        $lat = get_post_meta($sound_id, '_arb_latitude', true);
        $lon = get_post_meta($sound_id, '_arb_longitude', true);

        if (!$lat || !$lon) {
            // Remove from geo index if coordinates are missing
            self::delete_sound($sound_id);
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'arb_geo_index';

        $geohash = arb_geohash_encode($lat, $lon, 12);

        $wpdb->replace($table, [
            'sound_id'  => $sound_id,
            'latitude'  => $lat,
            'longitude' => $lon,
            'geohash'   => $geohash,
        ]);
    }

    /**
     * Delete sound from geo index
     */
    public static function delete_sound($sound_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'arb_geo_index';

        $wpdb->delete($table, ['sound_id' => $sound_id]);
    }

    /**
     * Get sounds in bounding box
     */
    public static function get_sounds_in_bbox($lat1, $lon1, $lat2, $lon2, $limit = 1000, $offset = 0) {
        global $wpdb;
        $table = $wpdb->prefix . 'arb_geo_index';

        // Ensure proper min/max
        $min_lat = min($lat1, $lat2);
        $max_lat = max($lat1, $lat2);
        $min_lon = min($lon1, $lon2);
        $max_lon = max($lon1, $lon2);

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT sound_id, latitude, longitude, geohash
             FROM {$table}
             WHERE latitude BETWEEN %f AND %f
               AND longitude BETWEEN %f AND %f
             LIMIT %d OFFSET %d",
            $min_lat,
            $max_lat,
            $min_lon,
            $max_lon,
            $limit,
            $offset
        ));

        return $results;
    }

    /**
     * Count sounds in bounding box
     */
    public static function count_sounds_in_bbox($lat1, $lon1, $lat2, $lon2) {
        global $wpdb;
        $table = $wpdb->prefix . 'arb_geo_index';

        $min_lat = min($lat1, $lat2);
        $max_lat = max($lat1, $lat2);
        $min_lon = min($lon1, $lon2);
        $max_lon = max($lon1, $lon2);

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table}
             WHERE latitude BETWEEN %f AND %f
               AND longitude BETWEEN %f AND %f",
            $min_lat,
            $max_lat,
            $min_lon,
            $max_lon
        ));
    }

    /**
     * Reindex all sounds
     */
    public static function reindex_all() {
        global $wpdb;

        $batch_size = 100;
        $page = 1;
        $total = 0;

        while (true) {
            $sounds = get_posts([
                'post_type'      => 'sound',
                'post_status'    => 'publish',
                'posts_per_page' => $batch_size,
                'paged'          => $page,
                'meta_query'     => [
                    'relation' => 'AND',
                    [
                        'key'     => '_arb_latitude',
                        'compare' => 'EXISTS',
                    ],
                    [
                        'key'     => '_arb_longitude',
                        'compare' => 'EXISTS',
                    ],
                ],
            ]);

            if (empty($sounds)) {
                break;
            }

            foreach ($sounds as $sound) {
                self::index_sound($sound->ID);
                $total++;
            }

            $page++;
        }

        return $total;
    }
}
