<?php
/**
 * Map Clustering
 */

if (!defined('ABSPATH')) exit;

class ARB_Clustering {

    /**
     * Cluster sounds in bounding box
     */
    public static function cluster_sounds($bbox, $zoom, $page = 1, $per_page = 100) {
        [$lat1, $lon1, $lat2, $lon2] = $bbox;

        // Get sounds in bbox
        $offset = ($page - 1) * $per_page;
        $results = ARB_Geo_Indexer::get_sounds_in_bbox($lat1, $lon1, $lat2, $lon2, $per_page, $offset);

        // Get geohash precision based on zoom level
        $geohash_precision = self::get_geohash_precision($zoom);

        // Group by geohash prefix
        $clusters = [];
        foreach ($results as $row) {
            $prefix = substr($row->geohash, 0, $geohash_precision);

            if (!isset($clusters[$prefix])) {
                $clusters[$prefix] = [
                    'geohash' => $prefix,
                    'sounds'  => [],
                    'count'   => 0,
                ];
            }

            $clusters[$prefix]['sounds'][] = [
                'id'  => $row->sound_id,
                'lat' => (float) $row->latitude,
                'lon' => (float) $row->longitude,
            ];
            $clusters[$prefix]['count']++;
        }

        // Format output
        $output = [];
        foreach ($clusters as $cluster) {
            if ($cluster['count'] > 1) {
                // Multiple sounds - create cluster
                $lats = array_column($cluster['sounds'], 'lat');
                $lons = array_column($cluster['sounds'], 'lon');

                $output[] = [
                    'type'   => 'cluster',
                    'lat'    => array_sum($lats) / count($lats),
                    'lon'    => array_sum($lons) / count($lons),
                    'count'  => $cluster['count'],
                    'bounds' => [
                        'north' => max($lats),
                        'south' => min($lats),
                        'east'  => max($lons),
                        'west'  => min($lons),
                    ],
                ];
            } else {
                // Single sound
                $sound = $cluster['sounds'][0];
                $sound_data = self::get_sound_data($sound['id']);

                $output[] = array_merge([
                    'type' => 'sound',
                    'id'   => $sound['id'],
                    'lat'  => $sound['lat'],
                    'lon'  => $sound['lon'],
                ], $sound_data);
            }
        }

        // Get total count
        $total = ARB_Geo_Indexer::count_sounds_in_bbox($lat1, $lon1, $lat2, $lon2);

        return [
            'clusters' => $output,
            'total'    => $total,
        ];
    }

    /**
     * Get geohash precision based on zoom level
     */
    private static function get_geohash_precision($zoom) {
        // Zoom level to geohash precision mapping
        // Lower zoom = wider view = less precision
        if ($zoom <= 4) return 2;
        if ($zoom <= 8) return 3;
        if ($zoom <= 12) return 4;
        if ($zoom <= 16) return 5;
        return 6;
    }

    /**
     * Get sound data for map marker
     */
    private static function get_sound_data($sound_id) {
        $post = get_post($sound_id);

        if (!$post) {
            return [];
        }

        return [
            'title'  => $post->post_title,
            'author' => get_the_author_meta('display_name', $post->post_author),
            'plays'  => (int) get_post_meta($sound_id, '_arb_plays_count', true),
            'likes'  => (int) get_post_meta($sound_id, '_arb_likes_count', true),
            'url'    => get_permalink($sound_id),
        ];
    }
}
