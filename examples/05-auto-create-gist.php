<?php

/**
 * Example 5: Auto-Create New Gist (without GIST_ID)
 * 
 * This example shows how to automatically create a new gist
 * without providing a GIST_ID upfront.
 */

use Illuminate\Support\Facades\Storage;

// OPTION 1: Using .env configuration
// Set these in your .env file:
// GIST_TOKEN=your_github_token_here
// GIST_AUTO_CREATE=true
// (no GIST_ID needed)

// OPTION 2: Configure programmatically in config/filesystems.php
/*
'disks' => [
    'gist' => [
        'driver' => 'gist',
        'token' => env('GIST_TOKEN'),
        'auto_create' => true, // Enable auto-creation
        'public' => false,
        'description' => 'Auto-created gist from Laravel',
    ],
],
*/

// Write your first file - this will automatically create a new gist
Storage::disk('gist')->put('welcome.txt', 'Hello from auto-created gist!');
echo "File uploaded! A new gist was created automatically.\n";

// Write more files - they will be added to the same gist
Storage::disk('gist')->put('data.json', json_encode(['status' => 'success']));
Storage::disk('gist')->put('notes.md', '# My Notes');

// Get the auto-created gist ID
/** @var \Saeedvir\LaravelGistStorage\GistAdapter $adapter */
$adapter = Storage::disk('gist')->getAdapter();
$gistId = $adapter->getGistId();
echo "Your new Gist ID: {$gistId}\n";
echo "View it at: https://gist.github.com/{$gistId}\n";

// Read files from the auto-created gist
$content = Storage::disk('gist')->get('welcome.txt');
echo "Content: {$content}\n";

// List all files
$files = Storage::disk('gist')->files();
echo "Files in gist:\n";
foreach ($files as $file) {
    echo "  - {$file}\n";
}

// IMPORTANT: Save this gist ID for future use!
// You can store it in your database or .env file:
// GIST_ID={$gistId}
// Then set auto_create to false and use the existing gist

// Example: Retrieve the gist ID and save it
file_put_contents(
    base_path('.env.gist_id'),
    "GIST_ID={$gistId}\n" .
    "# Use this ID in your .env file\n" .
    "# Then set GIST_AUTO_CREATE=false\n"
);
echo "\nGist ID saved to .env.gist_id\n";
