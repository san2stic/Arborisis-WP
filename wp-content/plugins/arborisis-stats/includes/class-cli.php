<?php
/**
 * WP-CLI Commands for Stats
 */

if (!defined('ABSPATH')) exit;

class ARB_Stats_CLI {

    /**
     * Aggregate plays into daily totals
     *
     * ## OPTIONS
     *
     * [--all]
     * : Aggregate all historical data
     *
     * [--date=<date>]
     * : Aggregate specific date (YYYY-MM-DD)
     *
     * ## EXAMPLES
     *
     *     wp arborisis aggregate-plays
     *     wp arborisis aggregate-plays --all
     *     wp arborisis aggregate-plays --date=2024-01-15
     */
    public static function aggregate_plays($args, $assoc_args) {
        if (isset($assoc_args['all'])) {
            WP_CLI::line('Aggregating all plays...');
            $count = ARB_Aggregator::aggregate_all_plays();
            WP_CLI::success("Aggregated plays for {$count} sounds");
        } elseif (isset($assoc_args['date'])) {
            $date = $assoc_args['date'];
            WP_CLI::line("Aggregating plays for {$date}...");
            $count = ARB_Aggregator::aggregate_plays_for_day($date);
            WP_CLI::success("Aggregated {$count} sounds");
        } else {
            // Default: yesterday
            WP_CLI::line('Aggregating plays for yesterday...');
            $count = ARB_Aggregator::aggregate_plays_for_day();
            WP_CLI::success("Aggregated {$count} sounds");
        }
    }

    /**
     * Compute trending scores for all sounds
     *
     * ## EXAMPLES
     *
     *     wp arborisis compute-trending
     */
    public static function compute_trending($args, $assoc_args) {
        WP_CLI::line('Computing trending scores...');
        $count = ARB_Aggregator::compute_trending_scores();
        WP_CLI::success("Updated trending scores for {$count} sounds");
    }

    /**
     * Cleanup old play events
     *
     * ## OPTIONS
     *
     * [--days=<days>]
     * : Keep events from last N days
     * ---
     * default: 90
     * ---
     *
     * ## EXAMPLES
     *
     *     wp arborisis cleanup-plays
     *     wp arborisis cleanup-plays --days=30
     */
    public static function cleanup_plays($args, $assoc_args) {
        global $wpdb;

        $days = isset($assoc_args['days']) ? (int) $assoc_args['days'] : 90;
        $table = $wpdb->prefix . 'arb_plays';

        WP_CLI::line("Cleaning up play events older than {$days} days...");

        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));

        WP_CLI::success("Deleted {$deleted} old play events");
    }

    /**
     * Warm cache for stats endpoints
     *
     * ## EXAMPLES
     *
     *     wp arborisis warm-cache
     */
    public static function warm_cache($args, $assoc_args) {
        WP_CLI::line('Warming cache...');

        // Global stats
        WP_CLI::line('- Global stats');
        $request = new WP_REST_Request('GET', '/arborisis/v1/stats/global');
        ARB_REST_Stats::global_stats($request);

        // Leaderboards
        $types = ['sounds', 'users'];
        $periods = ['7d', '30d', 'all'];

        foreach ($types as $type) {
            foreach ($periods as $period) {
                WP_CLI::line("- Leaderboard: {$type}/{$period}");
                $request = new WP_REST_Request('GET', '/arborisis/v1/stats/leaderboards');
                $request->set_param('type', $type);
                $request->set_param('period', $period);
                ARB_REST_Stats::leaderboards($request);
            }
        }

        WP_CLI::success('Cache warmed');
    }
}
