/**
 * @typedef {Object} Sound
 * @property {number} id - Sound ID
 * @property {string} title - Sound title
 * @property {string} description - Sound description
 * @property {string} thumbnail - Thumbnail image URL
 * @property {string} audioUrl - Audio file URL
 * @property {number} duration - Duration in seconds
 * @property {number} plays_count - Number of plays
 * @property {number} likes_count - Number of likes
 * @property {string[]} tags - Array of tag names
 * @property {string} author - Author display name
 * @property {string} author_username - Author username
 * @property {string} date - Publication date (ISO 8601)
 * @property {number|null} latitude - Recording latitude
 * @property {number|null} longitude - Recording longitude
 * @property {string|null} location_name - Human-readable location
 * @property {string} license - License type (CC0, CC-BY, etc.)
 * @property {string} format - Audio format (mp3, wav, flac, ogg)
 */

/**
 * @typedef {Object} User
 * @property {number} id - User ID
 * @property {string} name - Display name
 * @property {string} username - Username (slug)
 * @property {string} avatar - Avatar image URL
 * @property {number} sounds_count - Number of uploaded sounds
 * @property {number} total_plays - Total plays across all sounds
 * @property {number} total_likes - Total likes across all sounds
 * @property {string|null} bio - User biography
 * @property {string|null} website - Personal website URL
 * @property {string|null} twitter - Twitter handle
 * @property {string|null} instagram - Instagram handle
 */

/**
 * @typedef {Object} APIResponse
 * @property {Sound[]} sounds - Array of sounds
 * @property {number} total - Total number of results
 * @property {number} pages - Total number of pages
 */

/**
 * @typedef {Object} LeaderboardResponse
 * @property {Sound[]|undefined} sounds - Top sounds (if type=sounds)
 * @property {User[]|undefined} users - Top users (if type=users)
 */

/**
 * @typedef {Object} GlobalStats
 * @property {number} total_sounds - Total number of sounds in database
 * @property {number} total_plays - Total plays count
 * @property {number} total_users - Total number of users
 * @property {number} countries_count - Number of countries covered
 * @property {Array<{date: string, plays: number}>} timeline - Daily play counts for last 30 days
 */

/**
 * @typedef {Object} UploadPresignResponse
 * @property {boolean} success - Whether request succeeded
 * @property {string} upload_url - S3 presigned URL for upload
 * @property {string} upload_key - S3 object key
 * @property {string} public_url - Public URL after upload
 */

/**
 * @typedef {Object} UploadFinalizeResponse
 * @property {boolean} success - Whether finalization succeeded
 * @property {number} sound_id - Created sound post ID
 * @property {string} message - Success/error message
 */

export {};
