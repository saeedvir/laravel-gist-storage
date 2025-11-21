<?php

/**
 * Example 2: File Uploads in Laravel Controller
 * 
 * This example shows how to handle file uploads in a Laravel controller.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    /**
     * Upload a file to Gist storage
     */
    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        // Store the file on Gist
        $path = $request->file('file')->store('uploads', 'gist');

        return response()->json([
            'success' => true,
            'path' => $path,
            'message' => 'File uploaded successfully to Gist!',
        ]);
    }

    /**
     * Download a file from Gist storage
     */
    public function download(string $filename)
    {
        if (!Storage::disk('gist')->exists($filename)) {
            abort(404, 'File not found');
        }

        return Storage::disk('gist')->download($filename);
    }

    /**
     * Delete a file from Gist storage
     */
    public function delete(string $filename)
    {
        if (!Storage::disk('gist')->exists($filename)) {
            abort(404, 'File not found');
        }

        Storage::disk('gist')->delete($filename);

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully!',
        ]);
    }

    /**
     * List all files in Gist storage
     */
    public function list()
    {
        $files = Storage::disk('gist')->files();

        $fileDetails = collect($files)->map(function ($file) {
            return [
                'name' => $file,
                'size' => Storage::disk('gist')->size($file),
                'mime_type' => Storage::disk('gist')->mimeType($file),
            ];
        });

        return response()->json([
            'success' => true,
            'files' => $fileDetails,
        ]);
    }
}
