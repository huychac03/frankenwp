<?php
/**
 * Cloudflare R2 Integration for FrankenWP
 * 
 * This mu-plugin enables Cloudflare R2 integration with the S3 Uploads plugin
 * Based on: https://jackwhitworth.com/blog/cloudflare-r2-buckets-with-wordpress/
 * 
 * @package FrankenWP
 * @author Jack Whitworth (original implementation by GitHub user tedyw)
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Configure R2 constants early - this runs before WordPress config
if (!defined('S3_UPLOADS_ENDPOINT') && getenv('CLOUDFLARE_R2_ENDPOINT')) {
    define('S3_UPLOADS_ENDPOINT', getenv('CLOUDFLARE_R2_ENDPOINT'));
}

if (!defined('S3_UPLOADS_BUCKET') && getenv('CLOUDFLARE_R2_BUCKET')) {
    define('S3_UPLOADS_BUCKET', getenv('CLOUDFLARE_R2_BUCKET'));
}

if (!defined('S3_UPLOADS_BUCKET_URL') && getenv('CLOUDFLARE_R2_BUCKET_URL')) {
    define('S3_UPLOADS_BUCKET_URL', getenv('CLOUDFLARE_R2_BUCKET_URL'));
}

if (!defined('S3_UPLOADS_REGION')) {
    define('S3_UPLOADS_REGION', 'auto');
}

if (!defined('S3_UPLOADS_KEY') && getenv('CLOUDFLARE_R2_ACCESS_KEY')) {
    define('S3_UPLOADS_KEY', getenv('CLOUDFLARE_R2_ACCESS_KEY'));
}

if (!defined('S3_UPLOADS_SECRET') && getenv('CLOUDFLARE_R2_SECRET_KEY')) {
    define('S3_UPLOADS_SECRET', getenv('CLOUDFLARE_R2_SECRET_KEY'));
}

/**
 * Override S3 client parameters for Cloudflare R2 compatibility
 * 
 * This filter is essential for making the S3 Uploads plugin work with Cloudflare R2
 * instead of Amazon S3. It sets the custom endpoint and enables path-style endpoints.
 */
function frankenwp_s3_uploads_s3_client_params($params) {
    if (defined('S3_UPLOADS_ENDPOINT')) {
        $params["endpoint"] = S3_UPLOADS_ENDPOINT;
        $params["use_path_style_endpoint"] = true;
    }
    return $params;
}
add_filter("s3_uploads_s3_client_params", "frankenwp_s3_uploads_s3_client_params");

/**
 * Display admin notice about R2 configuration status
 */
 function frankenwp_r2_admin_notice() {
     if (!current_user_can('manage_options')) {
         return;
     }
     
     $screen = get_current_screen();
     if ($screen && $screen->base === 'upload') {
         $r2_configured = defined('S3_UPLOADS_ENDPOINT') && 
                         defined('S3_UPLOADS_BUCKET') && 
                         defined('S3_UPLOADS_KEY') && 
                         defined('S3_UPLOADS_SECRET');
         
         if ($r2_configured) {
             $bucket_name = defined('S3_UPLOADS_BUCKET') ? S3_UPLOADS_BUCKET : 'Unknown';
             echo '<div class="notice notice-success"><p>';
             echo '<strong>✅ Cloudflare R2:</strong> Configured and active. ';
             echo 'Media uploads will be stored in: <code>' . esc_html($bucket_name) . '</code>';
             echo '</p></div>';
         } else {
             echo '<div class="notice notice-warning"><p>';
             echo '<strong>⚠️ Cloudflare R2:</strong> Not fully configured. ';
             echo 'Check your wp-config.php R2 settings.';
             echo '</p></div>';
         }
     }
 }
add_action('admin_notices', 'frankenwp_r2_admin_notice');

/**
 * Add debug information for R2 configuration
 */
function frankenwp_r2_debug_info() {
    if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) {
        echo '<!-- FrankenWP R2 Debug -->';
        echo '<!-- S3_UPLOADS_ENDPOINT: ' . (defined('S3_UPLOADS_ENDPOINT') ? 'SET' : 'NOT SET') . ' -->';
        echo '<!-- S3_UPLOADS_BUCKET: ' . (defined('S3_UPLOADS_BUCKET') ? 'SET' : 'NOT SET') . ' -->';
        echo '<!-- S3_UPLOADS_KEY: ' . (defined('S3_UPLOADS_KEY') ? 'SET' : 'NOT SET') . ' -->';
        echo '<!-- S3_UPLOADS_SECRET: ' . (defined('S3_UPLOADS_SECRET') ? 'SET' : 'NOT SET') . ' -->';
    }
}
add_action('wp_head', 'frankenwp_r2_debug_info');
add_action('admin_head', 'frankenwp_r2_debug_info'); 