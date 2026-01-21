<?php
/**
 * Stats Aggregator
 */

if (!defined('ABSPATH'))
    exit;

class ARB_Aggregator
{

    /**
     * Aggregate plays for a specific day
     */
    public static function aggregate_plays_for_day($date = null)
    {
        global $wpdb;
        $plays_table = $wpdb->prefix . 'arb_plays';
        $daily_table = $wpdb->prefix . 'arb_plays_daily';

        if (!$date) {
            $date = date('Y-m-d', strtotime('-1 day'));
        }

        // Get plays count per sound for the day
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT sound_id, COUNT(*) as plays_count
             FROM {$plays_table}
             WHERE DATE(created_at) = %s
             GROUP BY sound_id",
            $date
        ));

        foreach ($results as $row) {
            $wpdb->replace($daily_table, [
                'sound_id' => $row->sound_id,
                'day' => $date,
                'plays_count' => $row->plays_count,
            ]);
        }

        return count($results);
    }

    /**
     * Aggregate all plays
     */
    public static function aggregate_all_plays()
    {
        global $wpdb;
        $plays_table = $wpdb->prefix . 'arb_plays';
        $daily_table = $wpdb->prefix . 'arb_plays_daily';

        // Clear existing aggregations
        $wpdb->query("TRUNCATE TABLE {$daily_table}");

        // Aggregate by day
        $wpdb->query(
            "INSERT INTO {$daily_table} (sound_id, day, plays_count)
             SELECT sound_id, DATE(created_at) as day, COUNT(*) as plays_count
             FROM {$plays_table}
             GROUP BY sound_id, day"
        );

        // Update cached counts on posts
        $totals = $wpdb->get_results(
            "SELECT sound_id, SUM(plays_count) as total
             FROM {$daily_table}
             GROUP BY sound_id"
        );

        foreach ($totals as $row) {
            update_post_meta($row->sound_id, '_arb_plays_count', (int) $row->total);
        }

        return count($totals);
    }

    /**
     * Compute trending scores
     */
    public static function compute_trending_scores()
    {
        global $wpdb;

        $sounds = get_posts([
            'post_type' => 'sound',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        ]);

        $updated = 0;
        foreach ($sounds as $sound_id) {
            $score = self::calculate_trending_score($sound_id);
            update_post_meta($sound_id, '_arb_trending_score', $score);
            $updated++;
        }

        return $updated;
    }

    /**
     * Calculate trending score for a sound
     */
    private static function calculate_trending_score($sound_id)
    {
        $plays_7d = self::get_plays_last_n_days($sound_id, 7);
        $likes = ARB_Likes_Manager::get_count($sound_id);
        $post = get_post($sound_id);

        if (!$post)
            return 1.0;

        // Age in days
        $age_days = (time() - strtotime($post->post_date)) / 86400;

        // Trending formula: (plays_7d * 2 + likes * 5) / (age_days + 1)
        $score = ($plays_7d * 2 + $likes * 5) / ($age_days + 1);

        return max(1.0, $score);
    }

    /**
     * Get plays for last N days
     */
    private static function get_plays_last_n_days($sound_id, $days)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'arb_plays_daily';

        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(plays_count)
             FROM {$table}
             WHERE sound_id = %d
               AND day >= DATE_SUB(CURDATE(), INTERVAL %d DAY)",
            $sound_id,
            $days
        ));

        return (int) $total;
    }

    /**
     * Get top sounds
     */
    public static function top_sounds($limit = 10, $period = 'all')
    {
        $args = [
            'post_type' => 'sound',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
        ];

        if ($period === '7d' || $period === '30d') {
            // Use trending score for recent periods
            $args['meta_key'] = '_arb_trending_score';
        } else {
            // Use total plays for all-time
            $args['meta_key'] = '_arb_plays_count';
        }

        $sounds = get_posts($args);

        return array_map(function ($post) {
            $plays_count = ARB_Plays_Tracker::get_count($post->ID);
            $likes_count = ARB_Likes_Manager::get_count($post->ID);
            $thumbnail = get_post_meta($post->ID, '_thumbnail_url', true);

            return [
                'id' => $post->ID,
                'title' => $post->post_title,
                'thumbnail' => $thumbnail ?: null,
                'plays_count' => $plays_count,
                'likes_count' => $likes_count,
                'plays' => $plays_count, // Backwards compatibility
                'likes' => $likes_count, // Backwards compatibility
            ];
        }, $sounds);
    }

    /**
     * Get top users
     */
    public static function top_users($limit = 10)
    {
        global $wpdb;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT post_author, COUNT(*) as sounds_count, SUM(meta_value) as total_plays
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type = 'sound'
               AND p.post_status = 'publish'
               AND pm.meta_key = '_arb_plays_count'
             GROUP BY post_author
             ORDER BY total_plays DESC
             LIMIT %d",
            $limit
        ));

        return array_map(function ($row) {
            $user = get_userdata($row->post_author);
            return [
                'id' => $row->post_author,
                'name' => $user ? $user->display_name : 'Unknown',
                'username' => $user ? $user->user_login : 'unknown',
                'sounds_count' => (int) $row->sounds_count,
                'total_plays' => (int) $row->total_plays,
                'plays' => (int) $row->total_plays, // Backwards compatibility
            ];
        }, $results);
    }

    /**
     * Get plays timeline
     */
    public static function plays_timeline($days = 30)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'arb_plays_daily';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT day, SUM(plays_count) as plays
             FROM {$table}
             WHERE day >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
             GROUP BY day
             ORDER BY day ASC",
            $days
        ));

        return array_map(function ($row) {
            return [
                'date' => $row->day,
                'plays' => (int) $row->plays,
            ];
        }, $results);
    }
}
