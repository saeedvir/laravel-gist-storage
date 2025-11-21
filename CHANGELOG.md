# Changelog

All notable changes to `laravel-gist-storage` will be documented in this file.

## v1.0.0 - 2025-11-22

### Added
- Initial release
- Laravel 11/12 support
- Flysystem v3 adapter implementation
- Full Storage facade integration
- Livewire file upload support
- PSR-4 autoloading and PSR-12 coding standards
- GistClient with full GitHub Gist API support
- Configuration publishing support
- Comprehensive documentation (README, INSTALLATION, QUICKSTART, PACKAGE-SUMMARY)
- PHPUnit test setup
- Example files for common use cases

### Features
- Store files on GitHub Gist seamlessly
- Read, write, delete, move, and copy operations
- Stream support for large files
- File metadata retrieval (size, MIME type)
- List all files in a gist
- Multiple gist management via GistClient
- Proper error handling with exceptions
- Efficient caching mechanism

### Fixed
- Configuration namespace issue in service provider
- Changed config merge from `filesystems.disks.gist` to `gist-storage` to prevent conflicts with Laravel's FilesystemServiceProvider

### Security
- Token-based authentication with GitHub API
- Support for secret gists
- Environment variable configuration for sensitive data
