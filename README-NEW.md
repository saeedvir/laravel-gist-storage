# Laravel Gist Storage

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-11%20%7C%2012-red)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE.md)

A powerful Laravel storage driver that enables you to store and manage files on GitHub Gist using Laravel's familiar Storage facade. Turn GitHub Gist into a free, reliable cloud storage solution for your Laravel applications.

## 📦 Package Summary

This package provides a **Flysystem v3** adapter for GitHub Gist, seamlessly integrating with Laravel's filesystem. Store files, images, JSON data, or any text-based content directly to GitHub Gist using the same Storage API you already know.

### What is GitHub Gist Storage?

GitHub Gist is a service that allows you to share code snippets and files. This package transforms Gist into a fully-functional storage backend for Laravel applications, giving you:

- **Free cloud storage** backed by GitHub's infrastructure
- **Version control** for all your stored files
- **Public or private** file storage options
- **CDN-ready URLs** for direct file access
- **No additional infrastructure** costs or setup

### Core Features

✅ **Laravel Storage Facade Integration** - Use `Storage::disk('gist')` just like any other disk  
✅ **Auto-Create Gists** - Automatically create new gists without pre-configuring a GIST_ID  
✅ **Flysystem v3 Compatible** - Modern, PSR-compliant filesystem adapter  
✅ **Full CRUD Operations** - Read, write, update, delete, list, and manage files  
✅ **Stream Support** - Handle large files with PHP streams  
✅ **Livewire Compatible** - Upload files directly from Livewire components  
✅ **Zero Dependencies** - Pure PHP implementation using only cURL  
✅ **Smart Caching** - Reduces API calls with intelligent metadata caching  
✅ **Laravel 11 & 12** - Full support for the latest Laravel versions  
✅ **PHP 8.1+** - Modern PHP features and type safety  

## 🚀 Key Benefits

### 1. **Zero Infrastructure Costs**
No need for AWS S3, DigitalOcean Spaces, or other paid storage services. GitHub Gist is completely free and reliable.

### 2. **Instant Setup**
Just add your GitHub token, and you're ready to go. No complex configurations or third-party accounts.

### 3. **Developer-Friendly**
Use the same Laravel Storage API you already know:
```php
Storage::disk('gist')->put('file.txt', 'content');
$content = Storage::disk('gist')->get('file.txt');
```

### 4. **Version Control Built-In**
Every change to your files is tracked by GitHub. View file history, rollback changes, and audit modifications.

### 5. **Public URL Access**
Get direct CDN-backed URLs for your files, perfect for sharing images, JSON APIs, or configuration files.

### 6. **Automatic Gist Creation**
Don't have a Gist ID? Enable `auto_create` and the package will create one for you on first upload.

### 7. **Production-Ready**
- Comprehensive error handling with Laravel exceptions
- Token format validation to catch configuration errors early  
- Smart cache invalidation to keep metadata synchronized
- Stream position handling for reliable large file uploads
- Proper boolean conversion for environment variables

### 8. **Multiple Use Cases**
- **Configuration Storage** - Store app configs, feature flags, or environment settings
- **Static Content** - Host JSON data, CSV files, or text documents  
- **User Uploads** - Handle file uploads from Livewire or controller forms
- **Public APIs** - Share data files with public gist URLs
- **Backup Storage** - Store database dumps, logs, or backup files
- **CDN Alternative** - Serve small assets without CDN costs

## 📥 Installation

```bash
composer require saeedvir/laravel-gist-storage
```

## ⚡ Quick Start

### 1. Configure Your Environment

Add to your `.env` file:

```env
GIST_TOKEN=ghp_your_github_personal_access_token
GIST_AUTO_CREATE=true  # Auto-create gist (no GIST_ID needed)
# OR
GIST_ID=your_existing_gist_id  # Use existing gist
```

**Get your token:** https://github.com/settings/tokens (requires `gist` scope)

### 2. Add Disk Configuration

In `config/filesystems.php`:

```php
'disks' => [
    'gist' => [
        'driver' => 'gist',
        'token' => env('GIST_TOKEN'),
        'gist_id' => env('GIST_ID'),  // Optional if auto_create is true
        'auto_create' => env('GIST_AUTO_CREATE', false),
        'public' => false,
        'description' => 'Laravel Gist Storage',
    ],
],
```

### 3. Start Using It!

```php
use Illuminate\Support\Facades\Storage;

// Write a file
Storage::disk('gist')->put('hello.txt', 'Hello from Laravel!');

// Read a file
$content = Storage::disk('gist')->get('hello.txt');

// Check if file exists
if (Storage::disk('gist')->exists('hello.txt')) {
    echo 'File exists!';
}

// Get file size
$size = Storage::disk('gist')->size('hello.txt');

// List all files
$files = Storage::disk('gist')->files();

// Delete a file
Storage::disk('gist')->delete('hello.txt');

// Get the auto-created gist ID
$gistId = Storage::disk('gist')->getAdapter()->getGistId();

// Dynamically change gist ID
Storage::disk('gist')->getAdapter()->setGistId('another_gist_id');
Storage::disk('gist')->put('file.txt', 'New content in different gist');
```

## 📚 Documentation

- **[INSTALLATION.md](INSTALLATION.md)** - Detailed setup and configuration guide
- **[QUICKSTART.md](QUICKSTART.md)** - 5-minute quick start tutorial  
- **[PACKAGE-SUMMARY.md](PACKAGE-SUMMARY.md)** - Complete technical documentation
- **[examples/](examples/)** - Ready-to-use code examples for common scenarios

## 🔧 Usage Examples

### Upload from Livewire Component

```php
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUploader extends Component
{
    use WithFileUploads;
    
    public $file;
    
    public function save()
    {
        $this->validate(['file' => 'required|file|max:10240']);
        
        $path = $this->file->store('uploads', 'gist');
        
        session()->flash('message', 'File uploaded to Gist!');
    }
}
```

### Store JSON Data

```php
$data = ['users' => 1000, 'status' => 'active'];
Storage::disk('gist')->put('stats.json', json_encode($data));

// Retrieve and decode
$stats = json_decode(Storage::disk('gist')->get('stats.json'), true);
```

### Handle Streams

```php
$stream = fopen('large-file.txt', 'r');
Storage::disk('gist')->writeStream('backup.txt', $stream);
fclose($stream);
```

### Switch Between Multiple Gists

```php
// Work with gist A
Storage::disk('gist')->getAdapter()->setGistId('gist_id_a');
Storage::disk('gist')->put('config.json', json_encode(['app' => 'A']));

// Switch to gist B
Storage::disk('gist')->getAdapter()->setGistId('gist_id_b');
Storage::disk('gist')->put('config.json', json_encode(['app' => 'B']));

// Copy from one gist to another
Storage::disk('gist')->getAdapter()->setGistId('source_gist');
$data = Storage::disk('gist')->get('data.txt');

Storage::disk('gist')->getAdapter()->setGistId('target_gist');
Storage::disk('gist')->put('data.txt', $data);
```

## 🎯 Requirements

- **PHP:** 8.1, 8.2, or 8.3  
- **Laravel:** 11.x or 12.x
- **Extensions:** `ext-curl`, `ext-json`
- **GitHub:** Personal Access Token with `gist` scope

## 🛡️ Security

- Store your `GIST_TOKEN` securely in `.env` - never commit it
- Use private gists for sensitive data
- Token format validation prevents invalid credentials
- All API requests use HTTPS with SSL verification

See [SECURITY.md](SECURITY.md) for security policy and reporting vulnerabilities.

## 🤝 Contributing

Contributions are welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## 📝 License

MIT License - see [LICENSE.md](LICENSE.md) for details.

## 👤 Author

**Saeedvir**  
GitHub: [@saeedvir](https://github.com/saeedvir)  
Email: saeed.es91@gmail.com

## ⭐ Support

If this package helps your project, please consider giving it a star on GitHub!

---

**Made with ❤️ for the Laravel community**
