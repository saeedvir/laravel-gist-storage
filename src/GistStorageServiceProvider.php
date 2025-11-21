<?php

namespace Saeedvir\LaravelGistStorage;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class GistStorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/gist-storage.php',
            'filesystems.disks.gist'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/gist-storage.php' => config_path('gist-storage.php'),
        ], 'gist-storage-config');

        // Extend filesystem with gist driver
        Storage::extend('gist', function ($app, $config) {
            // Validate required configuration
            if (empty($config['token'])) {
                throw new \InvalidArgumentException('Gist storage requires a GitHub token.');
            }

            if (empty($config['gist_id'])) {
                throw new \InvalidArgumentException('Gist storage requires a gist_id.');
            }

            // Create Gist client
            $client = new GistClient($config['token']);

            // Create Gist adapter
            $adapter = new GistAdapter($client, $config['gist_id']);

            // Create Flysystem filesystem
            $filesystem = new Filesystem($adapter, [
                'public' => $config['public'] ?? false,
                'description' => $config['description'] ?? '',
            ]);

            // Return Laravel filesystem adapter
            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}
