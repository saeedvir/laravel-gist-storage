<?php

/**
 * Example 4: Using GistClient Directly
 * 
 * This example shows advanced usage with the GistClient class.
 */

use Saeedvir\LaravelGistStorage\GistClient;

// Create a client instance
$client = new GistClient(env('GIST_TOKEN'));

// 1. Upload a file to a specific gist
$result = $client->upload(
    filename: 'example.txt',
    content: 'This is a test file',
    description: 'My uploaded file',
    public: false,
    gistId: env('GIST_ID')
);

echo "File uploaded successfully!\n";
echo "Gist URL: {$result['html_url']}\n";

// 2. Upload a local file
$result = $client->uploadFromFile(
    filePath: '/path/to/local/file.txt',
    description: 'Uploaded from local file',
    public: false,
    gistId: env('GIST_ID')
);

// 3. Download all files from a gist
$files = $client->download(env('GIST_ID'));
foreach ($files as $filename => $content) {
    echo "File: {$filename}\n";
    echo "Content: {$content}\n";
}

// 4. Download files to a directory
$savedFiles = $client->downloadToDir(
    gistId: env('GIST_ID'),
    directory: storage_path('app/gist-downloads')
);

foreach ($savedFiles as $filePath) {
    echo "Downloaded: {$filePath}\n";
}

// 5. List all your gists
$gists = $client->list(page: 1, perPage: 10);
foreach ($gists as $gist) {
    echo "Gist ID: {$gist['id']}\n";
    echo "Description: {$gist['description']}\n";
    echo "Public: " . ($gist['public'] ? 'Yes' : 'No') . "\n";
    echo "Files: " . count($gist['files']) . "\n";
    echo "---\n";
}

// 6. Update gist description
$client->updateDescription(
    gistId: env('GIST_ID'),
    newDescription: 'Updated description'
);

// 7. Get raw URL for a file
$rawUrl = $client->getRawUrl(
    gistId: env('GIST_ID'),
    filename: 'example.txt'
);
echo "Raw URL: {$rawUrl}\n";

// 8. Check if a gist exists
if ($client->checkGistId(env('GIST_ID'))) {
    echo "Gist exists and is accessible\n";
} else {
    echo "Gist not found or not accessible\n";
}

// 9. Get gist metadata
$gist = $client->getGist(env('GIST_ID'));
echo "Gist owner: {$gist['owner']['login']}\n";
echo "Created at: {$gist['created_at']}\n";
echo "Updated at: {$gist['updated_at']}\n";

// 10. Delete a file from gist
$client->deleteFile(
    gistId: env('GIST_ID'),
    filename: 'example.txt'
);

// 11. Delete an entire gist (use with caution!)
// $client->delete(env('GIST_ID'));
