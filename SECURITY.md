# Security Policy

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within Laravel Gist Storage, please send an email to saeedvir@gmail.com. All security vulnerabilities will be promptly addressed.

Please do not publicly disclose the issue until it has been addressed by the maintainers.

## Security Best Practices

When using Laravel Gist Storage, follow these security best practices:

### 1. Protect Your GitHub Token

- **Never commit** your GitHub Personal Access Token to version control
- Store the token in `.env` file only
- Use environment variables for production deployments
- Never expose the token in client-side code
- Rotate tokens periodically

### 2. Use Secret Gists for Sensitive Data

- Set `GIST_PUBLIC=false` in your `.env` file
- Use secret gists for any sensitive or proprietary data
- Remember: "Secret" doesn't mean "encrypted" - secret gists are unlisted but still accessible if someone has the URL

### 3. Token Permissions

- Use tokens with **minimum required scopes** (only "gist")
- Don't use tokens with unnecessary permissions (repo, admin, etc.)
- Create separate tokens for different applications
- Revoke unused tokens immediately

### 4. Input Validation

- Always validate file uploads before storing them
- Use Laravel's validation rules:
  ```php
  $request->validate([
      'file' => 'required|file|mimes:pdf,jpg,png|max:10240',
  ]);
  ```
- Sanitize filenames to prevent path traversal attacks
- Check file types and sizes

### 5. Access Control

- Implement proper authorization before allowing users to upload/delete files
- Use Laravel's policies and gates
- Don't expose Gist IDs publicly unless necessary
- Implement rate limiting for upload endpoints

### 6. Production Considerations

- Use HTTPS for all connections (enforced by GitHub API)
- Monitor API rate limits
- Implement proper error handling (don't expose sensitive error messages)
- Use Laravel's encrypted environment variables for sensitive configs

### 7. File Content

- Don't store passwords, API keys, or sensitive credentials in Gist
- Be aware that deleted gists may be cached/archived
- Consider encrypting sensitive data before storage
- Remember that public gists are searchable on GitHub

### 8. Code Reviews

- Review all code that handles file uploads
- Audit third-party dependencies regularly
- Keep the package updated to the latest version
- Subscribe to security advisories

## Example Secure Implementation

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureFileController extends Controller
{
    public function upload(Request $request)
    {
        // Authorize the user
        $this->authorize('upload-files');
        
        // Validate the upload
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,png,txt',
                'max:10240', // 10MB max
            ],
        ]);
        
        // Rate limiting
        if (RateLimiter::tooManyAttempts('upload:' . $request->ip(), 5)) {
            abort(429, 'Too many upload attempts.');
        }
        
        RateLimiter::hit('upload:' . $request->ip());
        
        // Sanitize filename
        $originalName = $request->file('file')->getClientOriginalName();
        $safeName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME))
            . '.'
            . $request->file('file')->getClientOriginalExtension();
        
        // Store with unique name
        $path = $request->file('file')->storeAs(
            'uploads',
            Str::uuid() . '_' . $safeName,
            'gist'
        );
        
        // Log the upload
        Log::info('File uploaded to Gist', [
            'user_id' => auth()->id(),
            'filename' => $safeName,
            'size' => $request->file('file')->getSize(),
        ]);
        
        return response()->json([
            'success' => true,
            'path' => $path,
        ]);
    }
}
```

## Disclosure Policy

- Report vulnerabilities privately to saeedvir@gmail.com
- Allow reasonable time for fixes before public disclosure
- Credit will be given to reporters in security advisories
- We aim to respond within 48 hours of receiving a report

## Known Security Considerations

1. **GitHub API Rate Limits**: The GitHub API has rate limits. Excessive requests may result in temporary blocking.

2. **Gist Immutability**: GitHub maintains history of all gist changes. "Deleting" a file updates the gist; old versions may still be accessible via history.

3. **No Server-Side Encryption**: Files are stored as-is on GitHub's servers. Implement your own encryption if needed.

4. **Public Gist Searchability**: Public gists are indexed and searchable. Never use public gists for sensitive data.

## Updates and Patches

Security patches will be released as soon as possible. To stay informed:

- Watch this repository on GitHub
- Follow release notes
- Subscribe to security advisories
- Keep your dependencies updated

## Contact

For security concerns, contact: saeedvir@gmail.com

For general issues, use [GitHub Issues](https://github.com/saeedvir/laravel-gist-storage/issues).
