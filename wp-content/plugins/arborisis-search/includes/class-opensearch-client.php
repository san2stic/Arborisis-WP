<?php
/**
 * OpenSearch Client Wrapper
 */

if (!defined('ABSPATH')) exit;

use OpenSearch\ClientBuilder;

class ARB_OpenSearch_Client {

    private static $instance = null;

    /**
     * Get OpenSearch client instance
     */
    public static function get() {
        if (self::$instance === null) {
            self::$instance = self::create_client();
        }
        return self::$instance;
    }

    /**
     * Create OpenSearch client
     */
    private static function create_client() {
        $host = getenv('OPENSEARCH_HOST') ?: 'localhost';
        $port = getenv('OPENSEARCH_PORT') ?: 9200;
        $user = getenv('OPENSEARCH_USER') ?: 'admin';
        $pass = getenv('OPENSEARCH_PASSWORD') ?: 'admin';

        $hosts = [
            [
                'host'   => $host,
                'port'   => $port,
                'scheme' => 'https',
                'user'   => $user,
                'pass'   => $pass,
            ],
        ];

        // Check if SSL verification should be enabled (production) or disabled (dev with self-signed certs)
        $ssl_verify = getenv('OPENSEARCH_SSL_VERIFY') !== 'false';

        try {
            $builder = ClientBuilder::create()
                ->setHosts($hosts);
            
            // Only disable SSL verification in development environments
            if (!$ssl_verify) {
                $builder->setSSLVerification(false);
            }
            
            return $builder->build();
        } catch (Exception $e) {
            error_log('OpenSearch client creation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if client is available
     */
    public static function is_available() {
        $client = self::get();
        if (!$client) return false;

        try {
            $client->ping();
            return true;
        } catch (Exception $e) {
            error_log('OpenSearch ping error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get index name
     */
    public static function get_index() {
        return getenv('OPENSEARCH_INDEX') ?: 'arborisis_sounds';
    }

    /**
     * Create index with mapping
     */
    public static function create_index() {
        $client = self::get();
        if (!$client) return false;

        $index = self::get_index();

        $params = [
            'index' => $index,
            'body'  => [
                'settings' => [
                    'number_of_shards'   => 2,
                    'number_of_replicas' => 1,
                    'analysis' => [
                        'analyzer' => [
                            'custom_text' => [
                                'type'      => 'custom',
                                'tokenizer' => 'standard',
                                'filter'    => ['lowercase', 'asciifolding', 'stop'],
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'id'             => ['type' => 'long'],
                        'title'          => [
                            'type'     => 'text',
                            'analyzer' => 'custom_text',
                            'fields'   => ['keyword' => ['type' => 'keyword']],
                        ],
                        'content'        => ['type' => 'text', 'analyzer' => 'custom_text'],
                        'tags'           => ['type' => 'keyword'],
                        'author_id'      => ['type' => 'long'],
                        'author_name'    => ['type' => 'keyword'],
                        'location'       => ['type' => 'geo_point'],
                        'location_name'  => ['type' => 'text'],
                        'license'        => ['type' => 'keyword'],
                        'duration'       => ['type' => 'float'],
                        'plays'          => ['type' => 'integer'],
                        'likes'          => ['type' => 'integer'],
                        'trending_score' => ['type' => 'float'],
                        'created_at'     => ['type' => 'date'],
                        'updated_at'     => ['type' => 'date'],
                    ],
                ],
            ],
        ];

        try {
            $client->indices()->create($params);
            return true;
        } catch (Exception $e) {
            error_log('OpenSearch index creation error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete index
     */
    public static function delete_index() {
        $client = self::get();
        if (!$client) return false;

        $index = self::get_index();

        try {
            $client->indices()->delete(['index' => $index]);
            return true;
        } catch (Exception $e) {
            error_log('OpenSearch index deletion error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Index exists
     */
    public static function index_exists() {
        $client = self::get();
        if (!$client) return false;

        $index = self::get_index();

        try {
            return $client->indices()->exists(['index' => $index]);
        } catch (Exception $e) {
            return false;
        }
    }
}
