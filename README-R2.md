# Cloudflare R2 Integration with FrankenWP

This setup integrates [Cloudflare R2](https://www.cloudflare.com/products/r2/) with FrankenWP for efficient media offloading, based on [Jack Whitworth's guide](https://jackwhitworth.com/blog/cloudflare-r2-buckets-with-wordpress/).

## 🌟 What is Cloudflare R2?

Cloudflare R2 allows developers to store large amounts of unstructured data without the costly egress bandwidth fees associated with typical cloud storage services. It's essentially the same as Amazon S3 but with:

- **Generous free tier**
- **Lower costs than S3**
- **S3-compatible API** (drop-in replacement)
- **No egress fees**

## 🔧 Prerequisites

1. **Cloudflare Account** with R2 enabled
2. **R2 Bucket** created and configured
3. **API Credentials** for your R2 bucket

## 📦 What's Included

FrankenWP automatically includes:

- ✅ **S3 Uploads Plugin** (HumanMade) - pre-installed
- ✅ **Cloudflare R2 Integration** - must-use plugin
- ✅ **Configuration helpers** - admin notices and debug info
- ✅ **Automatic setup** - just add your credentials

## 🚀 Quick Setup

### 1. Create Cloudflare R2 Bucket

1. Go to Cloudflare Dashboard → R2 → Overview
2. Create a new bucket
3. Make it publicly accessible (for media serving)

### 2. Create API Credentials

1. In Cloudflare: **R2 → Manage R2 API Tokens**
2. Click **"Create API Token"**
3. Set permissions to **"Object Read & Write"**
4. Save the **Access Key ID** and **Secret Access Key**

### 3. Configure FrankenWP

Add these constants to your `wp-config.php`:

```php
// Cloudflare R2 Configuration
define("S3_UPLOADS_ENDPOINT", "https://YOUR-ACCOUNT-ID.r2.cloudflarestorage.com");
define("S3_UPLOADS_BUCKET", "your-bucket-name");
define("S3_UPLOADS_BUCKET_URL", "https://your-bucket-name.your-account-id.r2.cloudflarestorage.com");
define("S3_UPLOADS_REGION", "auto");
define("S3_UPLOADS_KEY", "your-access-key-id");
define("S3_UPLOADS_SECRET", "your-secret-access-key");
```

### 4. Test the Integration

1. Go to **WordPress Admin → Media → Library**
2. Upload a test image
3. Check that the image URL points to your R2 bucket
4. Verify the file appears in your Cloudflare R2 dashboard

## 🐳 Docker Compose Configuration

You can also use environment variables in your `docker-compose.yml`:

```yaml
services:
  wordpress:
    # ... other config
    environment:
      # ... other environment variables
      CLOUDFLARE_R2_ENDPOINT: "https://your-account-id.r2.cloudflarestorage.com"
      CLOUDFLARE_R2_BUCKET: "your-bucket-name"
      CLOUDFLARE_R2_BUCKET_URL: "https://your-bucket-name.your-account-id.r2.cloudflarestorage.com"
      CLOUDFLARE_R2_ACCESS_KEY: "your-access-key-id"
      CLOUDFLARE_R2_SECRET_KEY: "your-secret-access-key"
```

Then in your `wp-config.php`:

```php
define("S3_UPLOADS_ENDPOINT", $_ENV['CLOUDFLARE_R2_ENDPOINT']);
define("S3_UPLOADS_BUCKET", $_ENV['CLOUDFLARE_R2_BUCKET']);
define("S3_UPLOADS_BUCKET_URL", $_ENV['CLOUDFLARE_R2_BUCKET_URL']);
define("S3_UPLOADS_REGION", "auto");
define("S3_UPLOADS_KEY", $_ENV['CLOUDFLARE_R2_ACCESS_KEY']);
define("S3_UPLOADS_SECRET", $_ENV['CLOUDFLARE_R2_SECRET_KEY']);
```

## 🔍 Troubleshooting

### Check Configuration Status

The integration includes built-in status checking:

1. **Admin Notices**: Visit **Media → Library** to see configuration status
2. **Debug Overlay**: Enable `WP_DEBUG=true` to see R2 status overlay
3. **WordPress Admin**: Look for Cloudflare R2 notices

### Common Issues

1. **CORS Errors**: Add your domain to R2 bucket CORS settings
2. **Permission Errors**: Ensure API key has "Object Read & Write" permissions
3. **Upload Failures**: Check credentials and bucket configuration
4. **URL Issues**: Verify `S3_UPLOADS_BUCKET_URL` is publicly accessible

### WP-CLI Commands

Migrate existing media to R2:

```bash
# Get into your FrankenWP container
docker exec -it frankenwp_wordpress_1 bash

# Upload existing media to R2
wp s3-uploads upload-directory --verbose
```

## 🎯 Benefits

- **Performance**: Media served from Cloudflare's global CDN
- **Cost Savings**: No egress fees, generous free tier
- **Scalability**: Handle large media libraries efficiently
- **Backup**: Media automatically stored off-site
- **FrankenWP Integration**: Works seamlessly with caching system

## 📚 References

- [Jack Whitworth's Cloudflare R2 Guide](https://jackwhitworth.com/blog/cloudflare-r2-buckets-with-wordpress/)
- [HumanMade S3 Uploads Plugin](https://github.com/humanmade/S3-Uploads)
- [Cloudflare R2 Documentation](https://developers.cloudflare.com/r2/)

## 🔐 Security Notes

- Store API credentials securely (use environment variables)
- Set appropriate CORS policies on your R2 bucket
- Consider using bucket policies to restrict access
- Regularly rotate API keys for production use 