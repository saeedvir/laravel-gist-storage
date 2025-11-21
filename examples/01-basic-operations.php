<?php

/**
 * Example 1: Basic File Operations
 * 
 * This example shows basic file operations using the Gist storage driver.
 */

use Illuminate\Support\Facades\Storage;

// Write a file to Gist
Storage::disk('gist')->put('hello.txt', 'Hello, World!');

// Read a file from Gist
$content = Storage::disk('gist')->get('hello.txt');
echo $content; // Output: Hello, World!

// Check if file exists
if (Storage::disk('gist')->exists('hello.txt')) {
    echo "File exists!\n";
}

// Get file size
$size = Storage::disk('gist')->size('hello.txt');
echo "File size: {$size} bytes\n";

// Delete a file
Storage::disk('gist')->delete('hello.txt');

// List all files in the gist
$files = Storage::disk('gist')->files();
foreach ($files as $file) {
    echo "File: {$file}\n";
}
