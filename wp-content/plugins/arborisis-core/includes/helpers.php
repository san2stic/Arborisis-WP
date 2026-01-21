<?php
/**
 * Helper functions
 */

if (!defined('ABSPATH'))
    exit;

/**
 * Get Redis client instance
 */
function arb_redis_client()
{
    static $redis = null;

    if ($redis === null) {
        try {
            $redis = new Redis();
            $redis->connect(
                getenv('REDIS_HOST') ?: 'localhost',
                getenv('REDIS_PORT') ?: 6379
            );

            $password = getenv('REDIS_PASSWORD');
            if ($password) {
                $redis->auth($password);
            }

            $db = getenv('REDIS_DB') ?: 0;
            $redis->select($db);

        } catch (Exception $e) {
            error_log('Redis connection error: ' . $e->getMessage());
            return null;
        }
    }

    return $redis;
}

/**
 * Delete Redis keys by pattern
 */
function arb_redis_delete_pattern($pattern)
{
    $redis = arb_redis_client();
    if (!$redis)
        return;

    try {
        $keys = $redis->keys($pattern);
        if (!empty($keys)) {
            $redis->del($keys);
        }
    } catch (Exception $e) {
        error_log('Redis delete pattern error: ' . $e->getMessage());
    }
}

/**
 * Cache get or compute with anti-thundering herd
 */
function arb_cache_get_or_compute($key, $ttl, $compute_fn)
{
    $redis = arb_redis_client();
    if (!$redis) {
        return $compute_fn();
    }

    try {
        $value = $redis->get($key);

        if ($value !== false) {
            return json_decode($value, true);
        }

        // Acquire lock to prevent stampede
        $lock_key = "{$key}:lock";
        $lock_acquired = $redis->set($lock_key, 1, ['NX', 'EX' => 10]);

        if (!$lock_acquired) {
            // Wait for lock to be released then retry
            sleep(1);
            return arb_cache_get_or_compute($key, $ttl, $compute_fn);
        }

        try {
            $data = $compute_fn();
            $redis->setex($key, $ttl, json_encode($data));
            return $data;
        } finally {
            $redis->del($lock_key);
        }
    } catch (Exception $e) {
        error_log('Cache get or compute error: ' . $e->getMessage());
        return $compute_fn();
    }
}

/**
 * Generate hash for fingerprinting
 */
function arb_generate_hash($string)
{
    return hash('sha256', $string);
}

/**
 * Get client IP address
 */
function arb_get_client_ip()
{
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
}

/**
 * Get user agent
 */
function arb_get_user_agent()
{
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

/**
 * Calculate Haversine distance between two coordinates
 */
function arb_haversine_distance($lat1, $lon1, $lat2, $lon2)
{
    $earth_radius = 6371; // km

    $dlat = deg2rad($lat2 - $lat1);
    $dlon = deg2rad($lon2 - $lon1);

    $a = sin($dlat / 2) * sin($dlat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dlon / 2) * sin($dlon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c;
}

/**
 * Real Geohash encoding
 * Based on standard Geohash algorithm
 */
function arb_geohash_encode($lat, $lon, $precision = 12)
{
    $chars = '0123456789bcdefghjkmnpqrstuvwxyz';
    $geohash = '';

    $minLat = -90;
    $maxLat = 90;
    $minLon = -180;
    $maxLon = 180;
    $even = true;
    $bit = 0;
    $ch = 0;

    while (strlen($geohash) < $precision) {
        if ($even) {
            $mid = ($minLon + $maxLon) / 2;
            if ($lon > $mid) {
                $ch |= (16 >> $bit);
                $minLon = $mid;
            } else {
                $maxLon = $mid;
            }
        } else {
            $mid = ($minLat + $maxLat) / 2;
            if ($lat > $mid) {
                $ch |= (16 >> $bit);
                $minLat = $mid;
            } else {
                $maxLat = $mid;
            }
        }

        $even = !$even;
        if ($bit < 4) {
            $bit++;
        } else {
            $geohash .= $chars[$ch];
            $bit = 0;
            $ch = 0;
        }
    }

    return $geohash;
}
