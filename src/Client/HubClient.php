<?php

namespace Prestoworld\MarketplaceSdk\Client;

use Prestoworld\MarketplaceSdk\Contracts\RepositoryProviderInterface;
use Prestoworld\MarketplaceSdk\Contracts\RepositoryItemInterface;
use Prestoworld\MarketplaceSdk\Common\MarketplaceExtension;

/**
 * Class HubClient
 * 
 * Strategy Pattern: Implements RepositoryProviderInterface for 
 * any remote Hub following the PrestoWorld Hub specification.
 */
class HubClient implements RepositoryProviderInterface
{
    protected string $hubUrl;
    protected string $accessToken;

    public function __construct(string $url, string $token = '')
    {
        $this->hubUrl = rtrim($url, '/');
        $this->accessToken = $token;
    }

    public function getProviderId(): string { return parse_url($this->hubUrl, PHP_URL_HOST) ?? 'remote-hub'; }

    public function fetchAll(array $filters = []): array
    {
        $type = $filters['type'] ?? 'plugin';
        $endpoint = ($type === 'theme') ? 'themes' : 'plugins';
        
        $query = http_build_query($filters);
        $data = $this->request("{$this->hubUrl}/api/{$endpoint}?{$query}");
        
        if (!isset($data['data']) || !is_array($data['data'])) {
            return [];
        }

        return array_map(function($item) use ($type) {
            $item['type'] = $type;
            return new MarketplaceExtension($item);
        }, $data['data']);
    }

    public function findBySlug(string $slug, string $type = 'any'): ?RepositoryItemInterface
    {
        // Try plugins then themes if type is any
        $endpoints = ($type === 'any') ? ['plugins', 'themes'] : [($type === 'theme' ? 'themes' : 'plugins')];
        
        foreach ($endpoints as $ep) {
            $data = $this->request("{$this->hubUrl}/api/{$ep}/{$slug}");
            if (!isset($data['error'])) {
                $data['type'] = ($ep === 'themes') ? 'theme' : 'plugin';
                return new MarketplaceExtension($data);
            }
        }
        
        return null;
    }

    public function count(array $filters = []): int
    {
        $type = $filters['type'] ?? 'plugin';
        $endpoint = ($type === 'theme') ? 'themes' : 'plugins';
        $data = $this->request("{$this->hubUrl}/api/{$endpoint}?" . http_build_query($filters));
        return $data['pagination']['total'] ?? count($data['data'] ?? []);
    }

    /**
     * Internal REST client.
     */
    protected function request(string $url): array
    {
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PrestoWorld Marketplace SDK',
                    'Accept: application/json',
                    $this->accessToken ? "Authorization: Bearer {$this->accessToken}" : ""
                ]
            ]
        ];

        $context = stream_context_create($opts);
        $result = @file_get_contents($url, false, $context);
        
        return $result ? json_decode($result, true) : ['error' => 'Connection failed'];
    }
}
