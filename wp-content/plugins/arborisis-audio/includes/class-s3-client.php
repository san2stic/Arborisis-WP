<?php
/**
 * S3 Client Wrapper
 */

if (!defined('ABSPATH')) exit;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class ARB_S3_Client {

    private static $instance = null;

    /**
     * Get S3 client instance
     */
    public static function get() {
        if (self::$instance === null) {
            self::$instance = self::create_client();
        }
        return self::$instance;
    }

    /**
     * Create S3 client
     */
    private static function create_client() {
        $endpoint = getenv('S3_ENDPOINT');
        $region   = getenv('S3_REGION') ?: 'us-east-1';
        $key      = getenv('S3_ACCESS_KEY');
        $secret   = getenv('S3_SECRET_KEY');

        if (!$endpoint || !$key || !$secret) {
            throw new Exception('S3 credentials not configured');
        }

        $config = [
            'version'     => 'latest',
            'region'      => $region,
            'endpoint'    => $endpoint,
            'credentials' => [
                'key'    => $key,
                'secret' => $secret,
            ],
            'use_path_style_endpoint' => true, // Required for MinIO compatibility
        ];

        return new S3Client($config);
    }

    /**
     * Generate presigned PUT URL
     */
    public static function create_presigned_put_url($key, $content_type, $expires = '+15 minutes') {
        // Create a separate client with public endpoint for presigned URL generation
        $public_endpoint = getenv('S3_PUBLIC_ENDPOINT') ?: getenv('S3_ENDPOINT');
        $region   = getenv('S3_REGION') ?: 'us-east-1';
        $key_id   = getenv('S3_ACCESS_KEY');
        $secret   = getenv('S3_SECRET_KEY');
        $bucket   = getenv('S3_BUCKET');

        $public_client = new S3Client([
            'version'     => 'latest',
            'region'      => $region,
            'endpoint'    => $public_endpoint,
            'credentials' => [
                'key'    => $key_id,
                'secret' => $secret,
            ],
            'use_path_style_endpoint' => true,
        ]);

        $cmd = $public_client->getCommand('PutObject', [
            'Bucket'      => $bucket,
            'Key'         => $key,
            'ContentType' => $content_type,
        ]);

        $request = $public_client->createPresignedRequest($cmd, $expires);
        return (string) $request->getUri();
    }

    /**
     * Check if object exists
     */
    public static function object_exists($key) {
        $client = self::get();
        $bucket = getenv('S3_BUCKET');

        try {
            $client->headObject([
                'Bucket' => $bucket,
                'Key'    => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }

    /**
     * Get object metadata
     */
    public static function get_object_metadata($key) {
        $client = self::get();
        $bucket = getenv('S3_BUCKET');

        try {
            $result = $client->headObject([
                'Bucket' => $bucket,
                'Key'    => $key,
            ]);
            return [
                'content_type'   => $result['ContentType'] ?? '',
                'content_length' => $result['ContentLength'] ?? 0,
                'last_modified'  => $result['LastModified'] ?? null,
            ];
        } catch (AwsException $e) {
            return null;
        }
    }

    /**
     * Download object to local file
     */
    public static function download_object($key, $destination) {
        $client = self::get();
        $bucket = getenv('S3_BUCKET');

        try {
            $client->getObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SaveAs' => $destination,
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('S3 download error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete object
     */
    public static function delete_object($key) {
        $client = self::get();
        $bucket = getenv('S3_BUCKET');

        try {
            $client->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            error_log('S3 delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get public URL for object
     */
    public static function get_public_url($key) {
        $endpoint = getenv('S3_PUBLIC_ENDPOINT') ?: getenv('S3_ENDPOINT');
        $endpoint = rtrim($endpoint, '/');
        $bucket   = getenv('S3_BUCKET');

        return "{$endpoint}/{$bucket}/{$key}";
    }
}
