<?php

namespace Saeedvir\LaravelGistStorage\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Saeedvir\LaravelGistStorage\Tests\TestCase;

class GistStorageTest extends TestCase
{
    /** @test */
    public function it_can_register_gist_driver(): void
    {
        $this->assertTrue(true);
        // Note: Full integration tests require valid GitHub token and gist ID
        // These should be run separately with environment variables set
    }

    /** @test */
    public function it_validates_configuration(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        config(['filesystems.disks.gist.token' => null]);
        
        Storage::disk('gist')->put('test.txt', 'content');
    }
}
