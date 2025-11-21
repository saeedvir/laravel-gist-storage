<?php

namespace Saeedvir\LaravelGistStorage;

use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use RuntimeException;
use Throwable;

/**
 * Flysystem v3 adapter for GitHub Gist storage
 * 
 * @author saeedvir
 * @license MIT
 */
class GistAdapter implements FilesystemAdapter
{
    private GistClient $client;
    private string $gistId;
    private array $fileCache = [];
    private bool $cacheLoaded = false;

    public function __construct(GistClient $client, string $gistId)
    {
        $this->client = $client;
        $this->gistId = $gistId;
    }

    /**
     * Load all files from gist into cache
     */
    private function loadCache(): void
    {
        if ($this->cacheLoaded) {
            return;
        }

        try {
            $gistData = $this->client->getGist($this->gistId);
            $this->fileCache = [];
            
            foreach ($gistData['files'] as $filename => $fileData) {
                $this->fileCache[$filename] = [
                    'size' => $fileData['size'],
                    'raw_url' => $fileData['raw_url'],
                    'type' => $fileData['type'] ?? 'text/plain',
                ];
            }
            
            $this->cacheLoaded = true;
        } catch (Throwable $e) {
            throw UnableToReadFile::fromLocation($this->gistId, 'Failed to load gist metadata: ' . $e->getMessage());
        }
    }

    /**
     * Invalidate cache
     */
    private function invalidateCache(): void
    {
        $this->cacheLoaded = false;
        $this->fileCache = [];
    }

    public function fileExists(string $path): bool
    {
        try {
            $this->loadCache();
            return isset($this->fileCache[$path]);
        } catch (Throwable $e) {
            throw UnableToCheckFileExistence::forLocation($path, $e);
        }
    }

    public function directoryExists(string $path): bool
    {
        // Gist doesn't support directories
        return false;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        try {
            $this->client->upload(
                filename: $path,
                content: $contents,
                description: $config->get('description', ''),
                public: $config->get('public', false),
                gistId: $this->gistId
            );
            
            $this->invalidateCache();
        } catch (Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        try {
            $streamContents = stream_get_contents($contents);
            
            if ($streamContents === false) {
                throw new RuntimeException('Failed to read stream contents');
            }
            
            $this->write($path, $streamContents, $config);
        } catch (Throwable $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function read(string $path): string
    {
        try {
            $files = $this->client->download($this->gistId);
            
            if (!isset($files[$path])) {
                throw new RuntimeException("File not found: {$path}");
            }
            
            return $files[$path];
        } catch (Throwable $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    public function readStream(string $path)
    {
        try {
            $content = $this->read($path);
            $stream = fopen('php://temp', 'r+');
            
            if ($stream === false) {
                throw new RuntimeException('Failed to open temporary stream');
            }
            
            fwrite($stream, $content);
            rewind($stream);
            
            return $stream;
        } catch (Throwable $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }

    public function delete(string $path): void
    {
        try {
            $this->client->deleteFile($this->gistId, $path);
            $this->invalidateCache();
        } catch (Throwable $e) {
            throw UnableToDeleteFile::atLocation($path, $e->getMessage(), $e);
        }
    }

    public function deleteDirectory(string $path): void
    {
        throw UnableToDeleteDirectory::atLocation($path, 'Gist does not support directories');
    }

    public function createDirectory(string $path, Config $config): void
    {
        throw UnableToCreateDirectory::atLocation($path, 'Gist does not support directories');
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Gist visibility is set at gist level, not file level');
    }

    public function visibility(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::visibility($path, 'Gist visibility is set at gist level, not file level');
    }

    public function mimeType(string $path): FileAttributes
    {
        try {
            $this->loadCache();
            
            if (!isset($this->fileCache[$path])) {
                throw new RuntimeException("File not found: {$path}");
            }
            
            return new FileAttributes(
                path: $path,
                mimeType: $this->fileCache[$path]['type'] ?? 'text/plain'
            );
        } catch (Throwable $e) {
            throw UnableToRetrieveMetadata::mimeType($path, $e->getMessage(), $e);
        }
    }

    public function lastModified(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::lastModified($path, 'Gist API does not provide file-level modification time');
    }

    public function fileSize(string $path): FileAttributes
    {
        try {
            $this->loadCache();
            
            if (!isset($this->fileCache[$path])) {
                throw new RuntimeException("File not found: {$path}");
            }
            
            return new FileAttributes(
                path: $path,
                fileSize: $this->fileCache[$path]['size']
            );
        } catch (Throwable $e) {
            throw UnableToRetrieveMetadata::fileSize($path, $e->getMessage(), $e);
        }
    }

    public function listContents(string $path, bool $deep): iterable
    {
        try {
            $this->loadCache();
            
            foreach ($this->fileCache as $filename => $fileData) {
                // If path is not root, skip files not in this "directory"
                if ($path !== '' && !str_starts_with($filename, $path . '/')) {
                    continue;
                }
                
                yield new FileAttributes(
                    path: $filename,
                    fileSize: $fileData['size'],
                    mimeType: $fileData['type'] ?? 'text/plain'
                );
            }
        } catch (Throwable $e) {
            // Return empty iterator on error
            return;
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        try {
            $content = $this->read($source);
            $this->write($destination, $content, $config);
            $this->delete($source);
        } catch (Throwable $e) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $e);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        try {
            $content = $this->read($source);
            $this->write($destination, $content, $config);
        } catch (Throwable $e) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $e);
        }
    }

    /**
     * Get the Gist ID
     */
    public function getGistId(): string
    {
        return $this->gistId;
    }

    /**
     * Get the GistClient instance
     */
    public function getClient(): GistClient
    {
        return $this->client;
    }
}
