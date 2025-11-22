# Quick Start Guide

Get up and running with Laravel Gist Storage in 5 minutes!

## 1. Install

```bash
composer require saeedvir/laravel-gist-storage
```

## 2. Get GitHub Token

1. Go to https://github.com/settings/tokens
2. Generate new token (classic)
3. Select "gist" scope
4. Copy the token

## 3. Create a Gist (Optional)

You have two options:

**Option A: Auto-create a new gist**
- Set `GIST_AUTO_CREATE=true` in `.env`
- Skip creating a gist manually
- The package will create one on first upload

**Option B: Use existing gist**
1. Go to https://gist.github.com/
2. Create a new gist
3. Copy the Gist ID from URL

Example: `https://gist.github.com/username/abc123def456` → ID is `abc123def456`

## 4. Configure

Add to `.env`:

```env
GIST_TOKEN=your_github_token_here
GIST_AUTO_CREATE=true  # Auto-create (recommended for new projects)
# OR
GIST_ID=your_gist_id_here  # Use existing gist
```

Add to `config/filesystems.php`:

```php
'disks' => [
    // ... other disks
    
    'gist' => [
        'driver' => 'gist',
        'token' => env('GIST_TOKEN'),
        'gist_id' => env('GIST_ID'),  // Optional if auto_create is true
        'auto_create' => env('GIST_AUTO_CREATE', false),
        'public' => env('GIST_PUBLIC', false),
        'description' => env('GIST_DESCRIPTION', 'Laravel files'),
    ],
],
```

## 5. Use It!

```php
use Illuminate\Support\Facades\Storage;

// Upload
Storage::disk('gist')->put('hello.txt', 'Hello World!');

// Read
$content = Storage::disk('gist')->get('hello.txt');

// Check existence
if (Storage::disk('gist')->exists('hello.txt')) {
    echo "File exists!";
}

// Delete
Storage::disk('gist')->delete('hello.txt');

// Dynamically switch gist ID
Storage::disk('gist')->getAdapter()->setGistId('another_gist_id');
Storage::disk('gist')->put('backup.txt', 'Backup data');
```

## Common Use Cases

### File Upload in Controller

```php
public function upload(Request $request)
{
    $request->validate(['file' => 'required|file|max:10240']);
    
    $path = $request->file('file')->store('uploads', 'gist');
    
    return response()->json(['path' => $path]);
}
```

### Livewire Upload

```php
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;
    
    public $file;
    
    public function save()
    {
        $this->validate(['file' => 'required|file']);
        
        $this->file->store('uploads', 'gist');
        
        session()->flash('message', 'Uploaded!');
    }
}
```

### Direct GistClient Usage

```php
use Saeedvir\LaravelGistStorage\GistClient;

$client = new GistClient(env('GIST_TOKEN'));

// Upload
$client->upload('file.txt', 'content', 'description', false, env('GIST_ID'));

// Download
$files = $client->download(env('GIST_ID'));

// List gists
$gists = $client->list(page: 1, perPage: 10);
```

## Need More Help?

- 📖 [Full Documentation](README.md)
- 💡 [Examples](examples/)
- 🔧 [Installation Guide](INSTALLATION.md)
- 🐛 [Troubleshooting](INSTALLATION.md#troubleshooting)

## That's It!

You're now storing files on GitHub Gist with Laravel! 🚀
