<?php

namespace Saeedvir\LaravelGistStorage\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Saeedvir\LaravelGistStorage\GistStorageServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            GistStorageServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default configuration
        $app['config']->set('filesystems.disks.gist', [
            'driver' => 'gist',
            'token' => env('GIST_TOKEN', 'fake-token-for-testing'),
            'gist_id' => env('GIST_ID', 'fake-gist-id'),
            'public' => false,
            'description' => 'Test gist',
        ]);
    }
}
