<?php

declare(strict_types=1);

namespace Prestoworld\MarketplaceSdk;

use Prestoworld\MarketplaceSdk\Exceptions\MarketplaceException;

class MarketplaceClient
{
    private string $baseUrl;

    private int $timeout;

    private array $headers;

    public function __construct(?string $baseUrl = null, int $timeout = 10)
    {
        $this->baseUrl = rtrim($baseUrl ?? 'https://prestoworld-marketplace.pages.dev', '/');
        $this->timeout = $timeout;
        $this->headers = [
            'User-Agent' => 'PrestoWorld-Marketplace-SDK/1.0',
            'Accept' => 'application/json',
        ];
    }

    public function setBaseUrl(string $url): void
    {
        $this->baseUrl = rtrim($url, '/');
    }

    public function setApiKey(string $key): void
    {
        $this->headers['Authorization'] = "Bearer {$key}";
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Browse marketplace items.
     *
     * @param  array{type?: string, category?: string, page?: int, per_page?: int, search?: string}  $params
     * @return array{data: array, pagination: array}
     */
    public function browse(array $params = []): array
    {
        $query = http_build_query(array_filter($params));
        return $this->get("/api/extensions?{$query}");
    }

    /**
     * Get item details by slug.
     */
    public function getItem(string $slug): ?array
    {
        try {
            return $this->get("/api/extensions/{$slug}/resolve?version=*");
        } catch (MarketplaceException $e) {
            return null;
        }
    }

    /**
     * Get version history for an item.
     */
    public function getVersions(string $slug): array
    {
        return $this->get("/api/extensions/{$slug}/versions");
    }

    /**
     * Check for updates to installed items.
     *
     * @param  array<array{slug: string, current_version: string, item_type?: string}>  $items
     * @return array<array{slug: string, name: string, latest_version: string, download_url: string, item_type: string}>
     */
    public function checkUpdates(array $items): array
    {
        $result = $this->post('/api/marketplace/check-updates', ['items' => $items]);
        return $result['updates'] ?? [];
    }

    /**
     * Get the download URL for a specific item version.
     */
    public function getDownloadUrl(string $slug, ?string $version = null): ?string
    {
        $params = ['download' => '1'];
        if ($version) {
            $params['version'] = $version;
        }
        // Return the redirect URL — the client should follow it
        return "{$this->baseUrl}/api/extensions/{$slug}/resolve?" . http_build_query($params);
    }

    /**
     * Proxy request to WordPress.org via marketplace proxy.
     */
    public function wporgProxy(string $endpoint, array $params = []): array
    {
        $query = http_build_query($params);
        return $this->get("/wporg/{$endpoint}?{$query}");
    }

    private function get(string $path): array
    {
        $url = $this->baseUrl . $path;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => $this->buildHeaderString(),
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw MarketplaceException::connectionFailed("GET {$url} failed");
        }

        return $this->decodeResponse($response, $url);
    }

    private function post(string $path, array $data): array
    {
        $url = $this->baseUrl . $path;
        $jsonBody = json_encode($data);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $this->buildHeaderString() . "Content-Type: application/json\r\n",
                'content' => $jsonBody,
                'timeout' => $this->timeout,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw MarketplaceException::connectionFailed("POST {$url} failed");
        }

        return $this->decodeResponse($response, $url);
    }

    private function buildHeaderString(): string
    {
        $lines = [];
        foreach ($this->headers as $key => $value) {
            $lines[] = "{$key}: {$value}";
        }
        return implode("\r\n", $lines) . "\r\n";
    }

    private function decodeResponse(string $body, string $url): array
    {
        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            throw MarketplaceException::invalidResponse("Invalid JSON from {$url}");
        }
        if (isset($decoded['error'])) {
            throw MarketplaceException::connectionFailed($decoded['error']);
        }
        return $decoded;
    }
}
