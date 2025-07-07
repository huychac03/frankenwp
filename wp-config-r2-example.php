<?php
/**
 * Cloudflare R2 Configuration for FrankenWP
 * 
 * Add these constants to your wp-config.php file to enable Cloudflare R2 integration
 * Based on: https://jackwhitworth.com/blog/cloudflare-r2-buckets-with-wordpress/
 * 
 * Instructions:
 * 1. Set up your Cloudflare R2 bucket and get API credentials
 * 2. Copy the constants below to your wp-config.php
 * 3. Replace the placeholder values with your actual R2 credentials
 * 4. Install the S3 Uploads plugin (handled automatically by FrankenWP)
 */

// ========================================
// CLOUDFLARE R2 CONFIGURATION
// ========================================

// Your Cloudflare Account ID can be found in the R2 dashboard
define("S3_UPLOADS_ENDPOINT", "https://YOUR-ACCOUNT-ID.r2.cloudflarestorage.com");

// Your R2 bucket name
define("S3_UPLOADS_BUCKET", "your-bucket-name");

// Your R2 bucket public URL (or custom domain)
define("S3_UPLOADS_BUCKET_URL", "https://your-bucket-name.your-account-id.r2.cloudflarestorage.com");

// Always use "auto" for Cloudflare R2
define("S3_UPLOADS_REGION", "auto");

// Your R2 API credentials (create these in Cloudflare dashboard)
define("S3_UPLOADS_KEY", "your-access-key-id");
define("S3_UPLOADS_SECRET", "your-secret-access-key");

// ========================================
// OPTIONAL: ENVIRONMENT-BASED CONFIG
// ========================================

// You can also use environment variables for better security:
/*
define("S3_UPLOADS_ENDPOINT", $_ENV['CLOUDFLARE_R2_ENDPOINT']);
define("S3_UPLOADS_BUCKET", $_ENV['CLOUDFLARE_R2_BUCKET']);
define("S3_UPLOADS_BUCKET_URL", $_ENV['CLOUDFLARE_R2_BUCKET_URL']);
define("S3_UPLOADS_REGION", "auto");
define("S3_UPLOADS_KEY", $_ENV['CLOUDFLARE_R2_ACCESS_KEY']);
define("S3_UPLOADS_SECRET", $_ENV['CLOUDFLARE_R2_SECRET_KEY']);
*/

// ========================================
// DOCKER COMPOSE ENVIRONMENT EXAMPLE
// ========================================

/*
Add these to your docker-compose.yml environment section:

environment:
  CLOUDFLARE_R2_ENDPOINT: "https://your-account-id.r2.cloudflarestorage.com"
  CLOUDFLARE_R2_BUCKET: "your-bucket-name"
  CLOUDFLARE_R2_BUCKET_URL: "https://your-bucket-name.your-account-id.r2.cloudflarestorage.com"
  CLOUDFLARE_R2_ACCESS_KEY: "your-access-key-id"
  CLOUDFLARE_R2_SECRET_KEY: "your-secret-access-key"
*/ 