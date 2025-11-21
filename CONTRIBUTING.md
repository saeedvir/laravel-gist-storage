# Contributing to Laravel Gist Storage

Thank you for considering contributing to Laravel Gist Storage! This document outlines the process for contributing to this package.

## Code of Conduct

Be respectful, inclusive, and constructive in all interactions. We're all here to make this package better.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates. When creating a bug report, include:

- **Clear title and description**
- **Steps to reproduce** the issue
- **Expected behavior** vs **actual behavior**
- **PHP version**, **Laravel version**, and **package version**
- **Error messages** or **stack traces**
- **Code samples** if applicable

### Suggesting Enhancements

Enhancement suggestions are welcome! Please provide:

- **Clear use case** for the enhancement
- **Expected benefits** to users
- **Potential implementation** approach (if you have ideas)
- **Examples** from other packages/libraries (if relevant)

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Install dependencies**: `composer install`
3. **Make your changes** following our coding standards
4. **Add tests** for new functionality
5. **Ensure tests pass**: `composer test`
6. **Update documentation** if needed
7. **Commit with clear messages** following conventional commits
8. **Submit a pull request**

## Development Setup

```bash
# Clone your fork
git clone https://github.com/your-username/laravel-gist-storage.git
cd laravel-gist-storage

# Install dependencies
composer install

# Run tests
composer test

# Run code style checks
composer format
```

## Coding Standards

### PSR Standards

- Follow **PSR-12** coding style
- Follow **PSR-4** autoloading standards
- Use **strict types** declaration in all PHP files

### Code Style

```php
<?php

declare(strict_types=1);

namespace Saeedvir\LaravelGistStorage;

class Example
{
    private string $property;

    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public function method(): string
    {
        return $this->property;
    }
}
```

### Documentation

- Add **PHPDoc comments** for all public methods
- Include **@param**, **@return**, and **@throws** annotations
- Provide **meaningful descriptions**

```php
/**
 * Upload a file to GitHub Gist
 *
 * @param string $filename The name of the file
 * @param string $content The file content
 * @param string $description Gist description
 * @return array Gist API response
 * @throws RuntimeException If upload fails
 */
public function upload(string $filename, string $content, string $description = ''): array
{
    // Implementation
}
```

## Testing

### Writing Tests

- Write tests for all new features
- Use **descriptive test method names**
- Follow **Arrange-Act-Assert** pattern
- Mock external dependencies

```php
/** @test */
public function it_can_upload_file_to_gist(): void
{
    // Arrange
    $adapter = new GistAdapter($this->mockClient, 'gist-id');
    
    // Act
    $adapter->write('test.txt', 'content', new Config());
    
    // Assert
    $this->assertTrue($adapter->fileExists('test.txt'));
}
```

### Running Tests

```bash
# Run all tests
composer test

# Run specific test
./vendor/bin/phpunit --filter test_name

# Run with coverage
composer test-coverage
```

## Git Workflow

### Branching Strategy

- `main` - Stable production code
- `develop` - Integration branch for features
- `feature/feature-name` - New features
- `bugfix/bug-name` - Bug fixes
- `hotfix/issue-name` - Urgent fixes

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: add support for multiple gist management
fix: resolve file deletion issue
docs: update installation guide
test: add tests for GistAdapter
refactor: improve error handling
chore: update dependencies
```

### Examples

```bash
# Good commits
git commit -m "feat: add stream support for large files"
git commit -m "fix: handle empty gist responses correctly"
git commit -m "docs: add Livewire integration examples"

# Bad commits
git commit -m "update stuff"
git commit -m "bug fix"
git commit -m "changes"
```

## Pull Request Process

1. **Update documentation** for any new features
2. **Add tests** with good coverage
3. **Update CHANGELOG.md** with your changes
4. **Ensure CI passes** (tests, code style)
5. **Request review** from maintainers
6. **Address feedback** promptly
7. **Squash commits** if requested

### PR Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
How has this been tested?

## Checklist
- [ ] Tests pass locally
- [ ] Code follows style guidelines
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
```

## Release Process

(For maintainers only)

1. Update version in `composer.json`
2. Update `CHANGELOG.md`
3. Create git tag: `git tag v1.x.x`
4. Push tag: `git push origin v1.x.x`
5. Create GitHub release with notes
6. Packagist will auto-update

## Code Review

All submissions require review. We look for:

- **Code quality** and maintainability
- **Test coverage** and quality
- **Documentation** completeness
- **Performance** implications
- **Security** considerations
- **Backward compatibility**

## Getting Help

- **Questions?** Open a [Discussion](https://github.com/saeedvir/laravel-gist-storage/discussions)
- **Issues?** Open an [Issue](https://github.com/saeedvir/laravel-gist-storage/issues)
- **Need help?** Ask in discussions or issues

## Recognition

Contributors will be recognized in:

- README.md contributors section
- Release notes
- GitHub contributors page

Thank you for contributing! 🎉
