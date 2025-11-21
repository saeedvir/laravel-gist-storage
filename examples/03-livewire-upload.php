<?php

/**
 * Example 3: Livewire File Upload Component
 * 
 * This example shows how to use Gist storage with Livewire file uploads.
 */

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class GistFileUploader extends Component
{
    use WithFileUploads;

    public $file;
    public $uploadedFiles = [];

    public function mount()
    {
        // Load existing files from Gist
        $this->loadFiles();
    }

    public function save()
    {
        // Validate the file
        $this->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        // Store the file on Gist with original filename
        $filename = $this->file->getClientOriginalName();
        $path = $this->file->storeAs('', $filename, 'gist');

        // Reset the file input
        $this->reset('file');

        // Reload files list
        $this->loadFiles();

        // Show success message
        session()->flash('message', "File '{$filename}' uploaded successfully to Gist!");
    }

    public function deleteFile($filename)
    {
        if (Storage::disk('gist')->exists($filename)) {
            Storage::disk('gist')->delete($filename);
            $this->loadFiles();
            session()->flash('message', "File '{$filename}' deleted successfully!");
        }
    }

    private function loadFiles()
    {
        $files = Storage::disk('gist')->files();
        
        $this->uploadedFiles = collect($files)->map(function ($file) {
            return [
                'name' => $file,
                'size' => Storage::disk('gist')->size($file),
                'size_human' => $this->formatBytes(Storage::disk('gist')->size($file)),
            ];
        })->toArray();
    }

    private function formatBytes($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function render()
    {
        return view('livewire.gist-file-uploader');
    }
}

/**
 * Blade View: resources/views/livewire/gist-file-uploader.blade.php
 * 
 * <div class="p-4">
 *     @if (session()->has('message'))
 *         <div class="alert alert-success mb-4">
 *             {{ session('message') }}
 *         </div>
 *     @endif
 * 
 *     <form wire:submit.prevent="save" class="mb-6">
 *         <div class="mb-4">
 *             <label class="block mb-2">Select File:</label>
 *             <input type="file" wire:model="file" class="border p-2">
 *             @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
 *         </div>
 * 
 *         <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
 *             Upload to Gist
 *         </button>
 * 
 *         <div wire:loading wire:target="file" class="ml-2">
 *             Uploading...
 *         </div>
 *     </form>
 * 
 *     <h3 class="text-lg font-bold mb-2">Uploaded Files:</h3>
 *     <ul class="space-y-2">
 *         @forelse ($uploadedFiles as $file)
 *             <li class="flex justify-between items-center border p-2">
 *                 <div>
 *                     <strong>{{ $file['name'] }}</strong>
 *                     <span class="text-gray-500">({{ $file['size_human'] }})</span>
 *                 </div>
 *                 <button 
 *                     wire:click="deleteFile('{{ $file['name'] }}')" 
 *                     class="bg-red-500 text-white px-3 py-1 rounded"
 *                 >
 *                     Delete
 *                 </button>
 *             </li>
 *         @empty
 *             <li class="text-gray-500">No files uploaded yet.</li>
 *         @endforelse
 *     </ul>
 * </div>
 */
