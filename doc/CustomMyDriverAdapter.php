<?php

namespace App\Storage;

use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Config;

/*

namespace App\Providers;

use App\Storage\CustomMyDriverAdapter;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class CustomStorageServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        app('filesystem')->extend('mydriver', function ($app, $config) {
            $adapter = new CustomMyDriverAdapter();

            return new Filesystem($adapter, $config);
        });
    }
}
*/
class CustomMyDriverAdapter implements FilesystemAdapter
{
    public function fileExists(string $path): bool
    {
        return file_exists($this->fullPath($path));
    }

    public function write(string $path, string $contents, Config $config): void
    {
        file_put_contents($this->fullPath($path), $contents);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $stream = fopen($this->fullPath($path), 'w+b');

        stream_copy_to_stream($contents, $stream);

        fclose($stream);
    }

    public function read(string $path): string
    {
        return file_get_contents($this->fullPath($path));
    }

    public function readStream(string $path)
    {
        return fopen($this->fullPath($path), 'rb');
    }

    public function delete(string $path): void
    {
        @unlink($this->fullPath($path));
    }

    public function createDirectory(string $path, Config $config): void
    {
        @mkdir($this->fullPath($path), 0775, true);
    }

    public function deleteDirectory(string $path): void
    {
        @rmdir($this->fullPath($path));
    }

    private function fullPath(string $path): string
    {
        return storage_path("custom/{$path}");
    }
}
