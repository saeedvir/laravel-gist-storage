# Installation Guide

This guide will walk you through installing and configuring the Laravel Gist Storage package.

## Requirements

- PHP 8.1 or higher
- Laravel 11.x or 12.x
- cURL extension enabled
- Composer
- GitHub account with Personal Access Token

## Step 1: Install via Composer

```bash
composer require saeedvir/laravel-gist-storage
```

The package will be automatically discovered by Laravel.

## Step 2: Publish Configuration (Optional)

If you want to customize the configuration file:

```bash
php artisan vendor:publish --tag=gist-storage-config
```

This will create `config/gist-storage.php` in your Laravel application.

## Step 3: Get GitHub Personal Access Token

1. Visit [GitHub Settings > Personal Access Tokens](https://github.com/settings/tokens)
2. Click **"Generate new token (classic)"**
3. Give your token a descriptive name (e.g., "Laravel Gist Storage")
4. Select the following scope:
   - ✅ **gist** - Create gists
5. Click **"Generate token"**
6. **Copy the token immediately** (you won't be able to see it again!)

## Step 4: Create a Gist

1. Go to [GitHub Gist](https://gist.github.com/)
2. Create a new gist:
   - Add a filename (e.g., `README.md`)
   - Add some initial content (can be empty)
   - Choose "Create secret gist" or "Create public gist"
3. After creating, copy the **Gist ID** from the URL
   - Example URL: `https://gist.github.com/username/a1b2c3d4e5f6g7h8i9j0`
   - Gist ID: `a1b2c3d4e5f6g7h8i9j0`

## Step 5: Configure Environment Variables

Add the following to your `.env` file:

```env
GIST_TOKEN=ghp_your_github_token_here
GIST_ID=your_gist_id_here
GIST_PUBLIC=false
GIST_DESCRIPTION="Files uploaded via Laravel"
```

**Security Note:** Never commit your `.env` file or expose your GitHub token!

## Step 6: Configure Filesystem Disk

Add the `gist` disk to your `config/filesystems.php`:

```php
'disks' => [
    
    // ... other disks (local, s3, etc.)

    'gist' => [
        'driver' => 'gist',
        'token' => env('GIST_TOKEN'),
        'gist_id' => env('GIST_ID'),
        'public' => env('GIST_PUBLIC', false),
        'description' => env('GIST_DESCRIPTION', 'Files uploaded via Laravel'),
    ],

],
```

**Note:** The package registers its configuration under the `gist-storage` namespace. The filesystem disk configuration uses environment variables to retrieve these settings.

## Step 7: Test the Installation

Create a test route in `routes/web.php`:

```php
use Illuminate\Support\Facades\Storage;

Route::get('/test-gist', function () {
    try {
        // Write a test file
        Storage::disk('gist')->put('test.txt', 'Hello from Laravel!');
        
        // Read it back
        $content = Storage::disk('gist')->get('test.txt');
        
        // Clean up
        Storage::disk('gist')->delete('test.txt');
        
        return "Success! Content: {$content}";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
```

Visit `/test-gist` in your browser. If you see "Success! Content: Hello from Laravel!", the installation is complete!

## Step 8: Set as Default Disk (Optional)

If you want to use Gist as your default filesystem:

In `config/filesystems.php`:

```php
'default' => env('FILESYSTEM_DISK', 'gist'),
```

Then you can use Storage without specifying the disk:

```php
Storage::put('file.txt', 'content'); // Uses gist by default
```

## Troubleshooting

### Error: "GitHub token is required"

Make sure `GIST_TOKEN` is set in your `.env` file and the config cache is cleared:

```bash
php artisan config:clear
```

### Error: "Gist storage requires a gist_id"

Make sure `GIST_ID` is set in your `.env` file with the correct Gist ID.

### Error: "GitHub API error (404)"

The Gist ID might be incorrect or the gist was deleted. Verify the gist exists at:
`https://gist.github.com/your_username/YOUR_GIST_ID`

### Error: "GitHub API error (401)"

Your token might be invalid or expired. Generate a new token with the "gist" scope.

### cURL SSL Certificate Error

If you encounter SSL certificate errors, make sure your PHP installation has up-to-date CA certificates.

## Next Steps

- Read the [README.md](README.md) for usage examples
- Check out the [examples](examples/) directory
- Review the configuration options in `config/gist-storage.php`

## Uninstallation

If you need to uninstall the package:

```bash
composer remove saeedvir/laravel-gist-storage
```

Remove the configuration from `config/filesystems.php` and delete `config/gist-storage.php` if you published it.
