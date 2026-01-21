<?php
/**
 * WP-CLI Commands for Audio
 */

if (!defined('ABSPATH')) exit;

class ARB_Audio_CLI {

    /**
     * Extract metadata for a sound
     *
     * ## OPTIONS
     *
     * <sound_id>
     * : The ID of the sound post
     *
     * ## EXAMPLES
     *
     *     wp arborisis extract-metadata 123
     */
    public static function extract_metadata($args, $assoc_args) {
        $sound_id = (int) $args[0];

        $sound = get_post($sound_id);

        if (!$sound || $sound->post_type !== 'sound') {
            WP_CLI::error("Sound #{$sound_id} not found");
            return;
        }

        WP_CLI::line("Extracting metadata for sound #{$sound_id}: {$sound->post_title}");

        $result = ARB_Metadata_Extractor::extract($sound_id);

        if (is_wp_error($result)) {
            WP_CLI::error($result->get_error_message());
            return;
        }

        WP_CLI::success("Metadata extracted successfully");
        WP_CLI::line("Duration: " . ($result['duration'] ?? 'N/A') . " seconds");
        WP_CLI::line("Format: " . ($result['format'] ?? 'N/A'));
        WP_CLI::line("Codec: " . ($result['codec'] ?? 'N/A'));
        WP_CLI::line("Sample Rate: " . ($result['sample_rate'] ?? 'N/A') . " Hz");
        WP_CLI::line("Channels: " . ($result['channels'] ?? 'N/A'));
    }
}
