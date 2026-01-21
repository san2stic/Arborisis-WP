<?php
/**
 * Graph Builder
 */

if (!defined('ABSPATH')) exit;

class ARB_Graph_Builder {

    /**
     * Build graph from seed sound
     */
    public static function build($seed_id, $depth = 2, $max_nodes = 50) {
        $visited = [];
        $nodes = [];
        $edges = [];

        $queue = [['id' => $seed_id, 'level' => 0]];

        while (!empty($queue) && count($nodes) < $max_nodes) {
            $current = array_shift($queue);
            $id = $current['id'];
            $level = $current['level'];

            if (isset($visited[$id]) || $level > $depth) {
                continue;
            }

            $visited[$id] = true;

            // Add node
            $node = self::get_node($id);
            if (!$node) continue;

            $nodes[] = $node;

            // Find neighbors if not at max depth
            if ($level < $depth) {
                $neighbors = self::find_neighbors($id, $max_nodes - count($nodes));

                foreach ($neighbors as $neighbor) {
                    $edges[] = [
                        'source' => $id,
                        'target' => $neighbor['id'],
                        'weight' => $neighbor['score'],
                        'type'   => $neighbor['type'],
                    ];

                    if (!isset($visited[$neighbor['id']])) {
                        $queue[] = ['id' => $neighbor['id'], 'level' => $level + 1];
                    }
                }
            }
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }

    /**
     * Get node data
     */
    private static function get_node($sound_id) {
        $post = get_post($sound_id);

        if (!$post || $post->post_type !== 'sound') {
            return null;
        }

        $tags = wp_get_object_terms($sound_id, 'sound_tag', ['fields' => 'names']);
        $lat = get_post_meta($sound_id, '_arb_latitude', true);
        $lon = get_post_meta($sound_id, '_arb_longitude', true);

        return [
            'id'     => $sound_id,
            'title'  => $post->post_title,
            'author' => get_the_author_meta('display_name', $post->post_author),
            'plays'  => (int) get_post_meta($sound_id, '_arb_plays_count', true),
            'likes'  => (int) get_post_meta($sound_id, '_arb_likes_count', true),
            'tags'   => $tags,
            'geo'    => ($lat && $lon) ? [
                'lat' => (float) $lat,
                'lon' => (float) $lon,
            ] : null,
        ];
    }

    /**
     * Find neighbor sounds based on similarity
     */
    private static function find_neighbors($sound_id, $limit = 10) {
        $tags = wp_get_object_terms($sound_id, 'sound_tag', ['fields' => 'ids']);
        $lat = get_post_meta($sound_id, '_arb_latitude', true);
        $lon = get_post_meta($sound_id, '_arb_longitude', true);

        // Query candidates with similar tags
        $args = [
            'post_type'      => 'sound',
            'post_status'    => 'publish',
            'posts_per_page' => $limit * 3, // Over-fetch for scoring
            'post__not_in'   => [$sound_id],
        ];

        if (!empty($tags)) {
            $args['tax_query'] = [[
                'taxonomy' => 'sound_tag',
                'field'    => 'term_id',
                'terms'    => $tags,
                'operator' => 'IN',
            ]];
        }

        $candidates = get_posts($args);

        // Score each candidate
        $scored = [];
        foreach ($candidates as $candidate) {
            $score = 0;
            $type = 'tags';

            // Tag similarity (Jaccard)
            $candidate_tags = wp_get_object_terms($candidate->ID, 'sound_tag', ['fields' => 'ids']);
            $intersection = count(array_intersect($tags, $candidate_tags));
            $union = count(array_unique(array_merge($tags, $candidate_tags)));
            $jaccard = $union > 0 ? $intersection / $union : 0;
            $score += $jaccard * 10;

            // Geo proximity
            $c_lat = get_post_meta($candidate->ID, '_arb_latitude', true);
            $c_lon = get_post_meta($candidate->ID, '_arb_longitude', true);

            if ($lat && $lon && $c_lat && $c_lon) {
                $distance = arb_haversine_distance($lat, $lon, $c_lat, $c_lon);
                if ($distance < 100) { // < 100km
                    $geo_score = (100 - $distance) / 10;
                    $score += $geo_score;
                    if ($geo_score > $jaccard * 10) {
                        $type = 'geo';
                    }
                }
            }

            // Popularity boost
            $plays = (int) get_post_meta($candidate->ID, '_arb_plays_count', true);
            $score += log($plays + 1) * 0.5;

            $scored[] = [
                'id'    => $candidate->ID,
                'score' => $score,
                'type'  => $type,
            ];
        }

        // Sort by score and limit
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($scored, 0, $limit);
    }
}
