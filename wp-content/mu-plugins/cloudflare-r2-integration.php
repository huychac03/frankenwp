<?php
/**
 * Plugin Name: Cloudflare R2 Integration
 * Description: Integrates Cloudflare R2 with WordPress using S3 Uploads plugin
 * Author: Custom (Based on Jack Whitworth's guide)
 * Version: 1.0.0
 * 
 * Based on: https://jackwhitworth.com/blog/cloudflare-r2-buckets-with-wordpress/
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cloudflare R2 Integration Class
 * 
 * This class handles the integration between WordPress and Cloudflare R2
 * using the HumanMade S3 Uploads plugin as a compatibility layer.
 */
class FrankenWP_Cloudflare_R2 {
    
    public function __construct() {
        // Hook into S3 Uploads plugin
        add_filter('s3_uploads_s3_client_params', array($this, 'modify_s3_client_params'));
        
        // Add admin notices for configuration status
        add_action('admin_notices', array($this, 'configuration_notices'));
        
        // Add debug information
        add_action('wp_footer', array($this, 'add_debug_info'));
    }
    
    /**
     * Modify S3 client parameters to work with Cloudflare R2
     * 
     * This is the key filter that makes S3 Uploads work with Cloudflare R2
     * Based on tedyw's solution from the GitHub discussion
     */
    public function modify_s3_client_params($params) {
        if (defined('S3_UPLOADS_ENDPOINT')) {
            $params['endpoint'] = S3_UPLOADS_ENDPOINT;
            $params['use_path_style_endpoint'] = true;
        }
        
        return $params;
    }
    
    /**
     * Show admin notices about R2 configuration status
     */
    public function configuration_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $screen = get_current_screen();
        if ($screen && $screen->id === 'upload') {
            
            if (!$this->is_s3_uploads_active()) {
                ?>
                <div class="notice notice-error">
                    <p><strong>Cloudflare R2 Integration:</strong> S3 Uploads plugin is required but not found. Please install <a href="https://github.com/humanmade/S3-Uploads" target="_blank">HumanMade's S3 Uploads plugin</a>.</p>
                </div>
                <?php
            } elseif (!$this->is_r2_configured()) {
                ?>
                <div class="notice notice-warning">
                    <p><strong>Cloudflare R2 Integration:</strong> R2 configuration incomplete. Please add the required constants to your wp-config.php file.</p>
                    <details>
                        <summary>Show configuration example</summary>
                        <pre style="background: #f1f1f1; padding: 10px; margin: 10px 0;">
// Add these to your wp-config.php
define("S3_UPLOADS_ENDPOINT", "https://&lt;account-id&gt;.r2.cloudflarestorage.com");
define("S3_UPLOADS_BUCKET", "&lt;bucket-name&gt;");
define("S3_UPLOADS_BUCKET_URL", "&lt;bucket-public-url&gt;");
define("S3_UPLOADS_REGION", "auto");
define("S3_UPLOADS_KEY", "&lt;access-key-id&gt;");
define("S3_UPLOADS_SECRET", "&lt;secret-access-key&gt;");
                        </pre>
                    </details>
                </div>
                <?php
            } else {
                ?>
                <div class="notice notice-success">
                    <p><strong>✅ Cloudflare R2:</strong> Successfully configured and ready for media offloading!</p>
                </div>
                <?php
            }
        }
    }
    
    /**
     * Check if S3 Uploads plugin is active
     */
    private function is_s3_uploads_active() {
        return function_exists('s3_uploads_init') || class_exists('S3_Uploads');
    }
    
    /**
     * Check if R2 is properly configured
     */
    private function is_r2_configured() {
        $required_constants = [
            'S3_UPLOADS_ENDPOINT',
            'S3_UPLOADS_BUCKET', 
            'S3_UPLOADS_BUCKET_URL',
            'S3_UPLOADS_KEY',
            'S3_UPLOADS_SECRET'
        ];
        
        foreach ($required_constants as $constant) {
            if (!defined($constant) || empty(constant($constant))) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Add debug information for R2 status
     */
    public function add_debug_info() {
        if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) {
            $status = $this->get_r2_status();
            
            echo '<!-- Cloudflare R2 Debug Info -->';
            echo '<div style="position: fixed; bottom: 50px; right: 10px; background: rgba(0,123,255,0.9); color: white; padding: 10px; font-size: 12px; z-index: 9998; border-radius: 4px; max-width: 300px;">';
            echo '<strong>📦 Cloudflare R2 Status:</strong><br>';
            echo 'S3 Uploads: ' . ($status['s3_uploads'] ? '✅ Active' : '❌ Missing') . '<br>';
            echo 'Configuration: ' . ($status['configured'] ? '✅ Complete' : '❌ Incomplete') . '<br>';
            if ($status['configured']) {
                echo 'Bucket: ' . (defined('S3_UPLOADS_BUCKET') ? S3_UPLOADS_BUCKET : 'Not set') . '<br>';
                echo 'Endpoint: ' . (defined('S3_UPLOADS_ENDPOINT') ? 'Configured' : 'Not set') . '<br>';
            }
            echo '</div>';
        }
    }
    
    /**
     * Get R2 integration status
     */
    public function get_r2_status() {
        return [
            's3_uploads' => $this->is_s3_uploads_active(),
            'configured' => $this->is_r2_configured(),
            'endpoint' => defined('S3_UPLOADS_ENDPOINT') ? S3_UPLOADS_ENDPOINT : null,
            'bucket' => defined('S3_UPLOADS_BUCKET') ? S3_UPLOADS_BUCKET : null,
        ];
    }
}

// Initialize the integration
new FrankenWP_Cloudflare_R2(); 