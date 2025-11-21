# Laravel Gist Storage - Package Summary

## Overview

**Laravel Gist Storage** is a fully-featured Laravel storage driver that enables you to store files on GitHub Gist using Laravel's Storage facade. Built with modern PHP standards and complete Laravel 11/12 compatibility.

## Package Structure

```
laravel-gist-storage/
├── src/                                    # Source code
│   ├── GistClient.php                      # GitHub Gist API client
│   ├── GistAdapter.php                     # Flysystem v3 adapter
│   └── GistStorageServiceProvider.php      # Laravel service provider
├── config/
│   └── gist-storage.php                    # Configuration file
├── tests/                                  # PHPUnit tests
│   ├── TestCase.php                        # Base test case
│   └── Feature/
│       └── GistStorageTest.php             # Feature tests
├── examples/                               # Usage examples
│   ├── 01-basic-operations.php             # Basic CRUD operations
│   ├── 02-controller-upload.php            # Laravel controller example
│   ├── 03-livewire-upload.php              # Livewire component example
│   └── 04-advanced-client.php              # Advanced GistClient usage
├── doc/                                    # Reference documentation
│   ├── GistClient.php                      # Original GistClient reference
│   └── CustomMyDriverAdapter.php           # Adapter implementation reference
├── .gitignore                              # Git ignore rules
├── CHANGELOG.md                            # Version history
├── CONTRIBUTING.md                         # Contribution guidelines
├── INSTALLATION.md                         # Installation guide
├── LICENSE.md                              # MIT License
├── QUICKSTART.md                           # Quick start guide
├── README.md                               # Main documentation
├── SECURITY.md                             # Security policy
├── composer.json                           # Package configuration
└── phpunit.xml                             # PHPUnit configuration
```

## Key Features

### ✅ Laravel 11/12 Compatible
- Full support for Laravel 11.x and 12.x
- Auto-discovery enabled
- Modern PHP 8.1+ syntax

### ✅ PSR-Compatible
- PSR-4 autoloading
- PSR-12 coding standards
- Type hints and strict types
- Clean, maintainable code

### ✅ Flysystem v3
- Implements `FilesystemAdapter` interface
- Full Flysystem v3 compatibility
- Stream support for large files
- Proper error handling with exceptions

### ✅ Storage Facade Support
- Works with `Storage::disk('gist')`
- All standard Storage methods
- Familiar Laravel API
- Easy migration from other drivers

### ✅ Livewire Upload Support
- Compatible with `WithFileUploads` trait
- Handles temporary uploads
- Works with Livewire file validation
- Supports multiple file uploads

## Core Components

### 1. GistClient
**Location:** `src/GistClient.php`

A standalone PHP class for interacting with GitHub Gist API.

**Methods:**
- `upload()` - Upload/update files
- `uploadFromFile()` - Upload from local file
- `download()` - Download all files
- `downloadToDir()` - Download to directory
- `list()` - List all gists
- `delete()` - Delete a gist
- `deleteFile()` - Delete a file from gist
- `updateDescription()` - Update gist description
- `getRawUrl()` - Get raw file URL
- `checkGistId()` - Validate gist ID
- `getGist()` - Get gist metadata

### 2. GistAdapter
**Location:** `src/GistAdapter.php`

Flysystem v3 adapter implementing `FilesystemAdapter` interface.

**Implemented Methods:**
- `fileExists()` - Check if file exists
- `write()` - Write file content
- `writeStream()` - Write from stream
- `read()` - Read file content
- `readStream()` - Read as stream
- `delete()` - Delete a file
- `fileSize()` - Get file size
- `mimeType()` - Get MIME type
- `listContents()` - List all files
- `move()` - Move/rename file
- `copy()` - Copy a file

**Features:**
- Efficient caching mechanism
- Proper error handling
- Stream support
- Metadata retrieval

### 3. GistStorageServiceProvider
**Location:** `src/GistStorageServiceProvider.php`

Laravel service provider for registering the driver.

**Responsibilities:**
- Register the `gist` driver
- Publish configuration
- Validate configuration
- Bootstrap the adapter

## Configuration

### Environment Variables
```env
GIST_TOKEN=your_github_token
GIST_ID=your_gist_id
GIST_PUBLIC=false
GIST_DESCRIPTION="Laravel files"
```

### Filesystem Disk
```php
'gist' => [
    'driver' => 'gist',
    'token' => env('GIST_TOKEN'),
    'gist_id' => env('GIST_ID'),
    'public' => env('GIST_PUBLIC', false),
    'description' => env('GIST_DESCRIPTION', 'Laravel files'),
],
```

## Usage Examples

### Basic Operations
```php
Storage::disk('gist')->put('file.txt', 'content');
Storage::disk('gist')->get('file.txt');
Storage::disk('gist')->exists('file.txt');
Storage::disk('gist')->delete('file.txt');
```

### File Uploads
```php
$request->file('upload')->store('uploads', 'gist');
```

### Livewire
```php
$this->file->store('uploads', 'gist');
```

### Direct Client
```php
$client = new GistClient($token);
$client->upload('file.txt', 'content', 'description', false, $gistId);
```

## Dependencies

### Required
- `php: ^8.1|^8.2|^8.3`
- `illuminate/support: ^11.0|^12.0`
- `illuminate/filesystem: ^11.0|^12.0`
- `league/flysystem: ^3.0`
- `ext-curl: *`
- `ext-json: *`

### Development
- `orchestra/testbench: ^9.0`
- `phpunit/phpunit: ^10.0|^11.0`

## Testing

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage
```

## Documentation

| Document | Description |
|----------|-------------|
| [README.md](README.md) | Main documentation with features and examples |
| [INSTALLATION.md](INSTALLATION.md) | Step-by-step installation guide |
| [QUICKSTART.md](QUICKSTART.md) | 5-minute quick start guide |
| [SECURITY.md](SECURITY.md) | Security best practices and policy |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Contribution guidelines |
| [CHANGELOG.md](CHANGELOG.md) | Version history and changes |

## Security Considerations

1. **Never commit** GitHub tokens to version control
2. **Use secret gists** for sensitive data
3. **Validate all uploads** before storage
4. **Implement rate limiting** on upload endpoints
5. **Monitor API usage** to avoid rate limits
6. **Encrypt sensitive data** before storage
7. **Review security policy** in SECURITY.md

## Support

- **Documentation:** [README.md](README.md)
- **Examples:** [examples/](examples/)
- **Issues:** GitHub Issues
- **Discussions:** GitHub Discussions

## License

MIT License - see [LICENSE.md](LICENSE.md)

## Author

**Saeedvir**
- GitHub: [@saeedvir](https://github.com/saeedvir)
- Email: saeedvir@gmail.com

## Acknowledgments

Built with:
- Laravel Framework
- Flysystem by The PHP League
- GitHub Gist API

---

**Version:** 1.0.0  
**Status:** Production Ready  
**Laravel:** 11.x, 12.x  
**PHP:** 8.1+  
**License:** MIT
