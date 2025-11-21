<?php
use RuntimeException;
use InvalidArgumentException;

/**
 * Full-featured GitHub Gist API client (pure PHP, no dependencies)
 * Supports: create, update, upload, download, list, delete
 *
 * @version 2.0
 * @author saeedvir <https://github.com/saeedvir>
 * @license MIT
 *
 * Requirements:
 *   - PHP 8.1+
 *   - cURL enabled
 *   - GitHub Personal Access Token with "gist" scope
 *   - https://github.com/settings/personal-access-tokens
 */

class GistClient
{
    private string $token;
    private string $apiBase = 'https://api.github.com';

    public function __construct(string $token)
    {
        $this->token = trim($token);
        if ($this->token === '') {
            throw new InvalidArgumentException('GitHub token is required.');
        }
    }

    /**
     * Create or update a gist (from raw content)
     */
    public function upload(
        string $filename,
        string $content,
        string $description = '',
        bool $public = false,
        ?string $gistId = null
    ): array {
        $url = $gistId ? "{$this->apiBase}/gists/{$gistId}" : "{$this->apiBase}/gists";
        $data = [
            'description' => $description,
            'public'      => $public,
            'files'       => [$filename => ['content' => $content]],
        ];
        $method = $gistId ? 'PATCH' : 'POST';
        return $this->request($url, $method, $data);
    }

    /**
     * Upload a local file
     */
    public function uploadFromFile(
        string $filePath,
        string $description = '',
        bool $public = false,
        ?string $gistId = null
    ): array {
        if (!is_file($filePath)) {
            throw new InvalidArgumentException("File not found: {$filePath}");
        }

        $filename = basename($filePath);
        $content = file_get_contents($filePath);

        return $this->upload($filename, $content, $description, $public, $gistId);
    }

    /**
     * Download all gist files (returns [filename => content])
     */
    public function download(string $gistId): array
    {
        $url = "{$this->apiBase}/gists/{$gistId}";
        $result = $this->request($url);

        $files = [];

        foreach ($result['files'] as $name => $file) {
            $rawUrl = $file['raw_url'];

            $ch = curl_init($rawUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    "User-Agent: PHP-GistClient/2.0",
                    "Authorization: token {$this->token}",
                    "Accept: application/vnd.github.v3.raw",
                ],
                CURLOPT_TIMEOUT => 30,
            ]);

            $content = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                throw new RuntimeException("cURL error while downloading {$name}: {$error}");
            }

            if ($code >= 400 || !$content) {
                throw new RuntimeException("Failed to download {$name} (HTTP {$code})");
            }

            $files[$name] = $content;
        }

        return $files;
    }


    /**
     * Download gist files and save locally
     */
    public function downloadToDir(string $gistId, string $directory): array
    {
        // Ensure directory exists or create it
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new RuntimeException("Failed to create directory: {$directory}");
            }
        }

        // Get all files from the gist (now uses cURL)
        $files = $this->download($gistId);
        $saved = [];

        foreach ($files as $name => $content) {
            $path = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $name;

            // Attempt to write file
            if (file_put_contents($path, $content) === false) {
                throw new RuntimeException("Failed to save file: {$path}");
            }

            $saved[] = $path;
        }

        return $saved;
    }


    /**
     * List user's gists (returns array of gist metadata)
     */
    public function list(int $page = 1, int $perPage = 10): array
    {
        $url = "{$this->apiBase}/gists?page={$page}&per_page={$perPage}";
        return $this->request($url);
    }

    /**
     * Update gist description
     */
    public function updateDescription(string $gistId, string $newDescription): array
    {
        $url = "{$this->apiBase}/gists/{$gistId}";
        $data = ['description' => $newDescription];
        return $this->request($url, 'PATCH', $data);
    }

    /**
     * Delete gist
     */
    public function delete(string $gistId): bool
    {
        $url = "{$this->apiBase}/gists/{$gistId}";
        $this->request($url, 'DELETE');
        return true;
    }

    /**
     * Private HTTP request handler
     */
    private function request(string $url, string $method = 'GET', ?array $data = null): array
    {
        $ch = curl_init($url);

        $headers = [
            "User-Agent: PHP-GistClient/2.0",
            "Authorization: token {$this->token}",
            "Accept: application/vnd.github+json",
        ];

        if ($data !== null) {
            $payload = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $headers[] = "Content-Type: application/json";
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        curl_setopt_array($ch, [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_TIMEOUT         => 30,
        ]);

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new RuntimeException("cURL error: {$error}");
        }

        if ($code === 204) {
            // DELETE success (no content)
            return [];
        }

        $decoded = json_decode($response, true);
        if ($code >= 400) {
            throw new RuntimeException("GitHub API error ({$code}) while requesting {$url}: " . json_encode($decoded));
        }

        return $decoded ?? [];
    }

    /**
     * Get the raw URL of a file in a gist
     *
     * @param string $gistId The Gist ID (e.g. "a1b2c3d4e5...")
     * @param string $filename The filename of the desired file
     * @return string|null The raw URL of the file, or null if not found
     */
    public function getRawUrl(string $gistId, string $filename): ?string
    {
        $data = $this->request("{$this->apiBase}/gists/{$gistId}");
        return $data['files'][$filename]['raw_url'] ?? null;
    }

    /**
     * Check if a GitHub Gist ID exists and is accessible
     *
     * @param string $gistId  The Gist ID (e.g. "a1b2c3d4e5...")
     * @return bool           True if gist exists, false otherwise
     */
    public function checkGistId(string $gistId): bool
    {
        $url = "{$this->apiBase}/gists/{$gistId}";
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "User-Agent: PHP-GistChecker/1.0",
                "Authorization: token {$this->token}",
                "Accept: application/vnd.github+json",
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}
