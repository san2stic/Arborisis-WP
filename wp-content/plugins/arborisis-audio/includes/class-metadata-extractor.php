<?php
/**
 * Audio Metadata Extraction using ffprobe
 */

if (!defined('ABSPATH')) exit;

class ARB_Metadata_Extractor {

    /**
     * Extract metadata from audio file
     */
    public static function extract($sound_id) {
        $key = get_post_meta($sound_id, '_arb_audio_key', true);

        if (!$key) {
            return new WP_Error('no_key', 'Audio key not found');
        }

        // Download to temp file
        $temp_file = tempnam(sys_get_temp_dir(), 'arb_audio_');

        if (!ARB_S3_Client::download_object($key, $temp_file)) {
            @unlink($temp_file);
            return new WP_Error('download_failed', 'Failed to download audio file');
        }

        // Extract metadata using ffprobe
        $metadata = self::extract_with_ffprobe($temp_file);

        // Clean up temp file
        @unlink($temp_file);

        if (is_wp_error($metadata)) {
            return $metadata;
        }

        // Update post meta
        if (isset($metadata['duration'])) {
            update_post_meta($sound_id, '_arb_duration', (float) $metadata['duration']);
        }

        if (isset($metadata['format'])) {
            update_post_meta($sound_id, '_arb_format', $metadata['format']);
        }

        if (isset($metadata['bitrate'])) {
            update_post_meta($sound_id, '_arb_bitrate', (int) $metadata['bitrate']);
        }

        if (isset($metadata['sample_rate'])) {
            update_post_meta($sound_id, '_arb_sample_rate', (int) $metadata['sample_rate']);
        }

        if (isset($metadata['channels'])) {
            update_post_meta($sound_id, '_arb_channels', (int) $metadata['channels']);
        }

        return $metadata;
    }

    /**
     * Extract metadata using ffprobe
     */
    private static function extract_with_ffprobe($file_path) {
        // Check if ffprobe is available
        $ffprobe = self::find_ffprobe();

        if (!$ffprobe) {
            return new WP_Error('ffprobe_not_found', 'ffprobe not found on system');
        }

        // Build ffprobe command
        $cmd = sprintf(
            '%s -v quiet -print_format json -show_format -show_streams %s 2>&1',
            escapeshellcmd($ffprobe),
            escapeshellarg($file_path)
        );

        // Execute command
        exec($cmd, $output, $return_code);

        if ($return_code !== 0) {
            return new WP_Error('ffprobe_failed', 'ffprobe execution failed');
        }

        // Parse JSON output
        $json = implode('', $output);
        $data = json_decode($json, true);

        if (!$data) {
            return new WP_Error('ffprobe_parse_failed', 'Failed to parse ffprobe output');
        }

        // Extract relevant metadata
        $metadata = [];

        // Duration
        if (isset($data['format']['duration'])) {
            $metadata['duration'] = (float) $data['format']['duration'];
        }

        // Format name
        if (isset($data['format']['format_name'])) {
            $metadata['format'] = $data['format']['format_name'];
        }

        // Bitrate
        if (isset($data['format']['bit_rate'])) {
            $metadata['bitrate'] = (int) $data['format']['bit_rate'];
        }

        // Audio stream info
        if (isset($data['streams']) && is_array($data['streams'])) {
            foreach ($data['streams'] as $stream) {
                if (isset($stream['codec_type']) && $stream['codec_type'] === 'audio') {
                    if (isset($stream['sample_rate'])) {
                        $metadata['sample_rate'] = (int) $stream['sample_rate'];
                    }
                    if (isset($stream['channels'])) {
                        $metadata['channels'] = (int) $stream['channels'];
                    }
                    if (isset($stream['codec_name'])) {
                        $metadata['codec'] = $stream['codec_name'];
                    }
                    break;
                }
            }
        }

        return $metadata;
    }

    /**
     * Find ffprobe binary
     */
    private static function find_ffprobe() {
        // Common locations
        $locations = [
            '/usr/bin/ffprobe',
            '/usr/local/bin/ffprobe',
            '/opt/homebrew/bin/ffprobe',
            'ffprobe', // PATH
        ];

        foreach ($locations as $location) {
            if (is_executable($location)) {
                return $location;
            }

            // Try which command
            $which = trim(shell_exec("which {$location} 2>/dev/null"));
            if ($which && is_executable($which)) {
                return $which;
            }
        }

        return null;
    }
}
