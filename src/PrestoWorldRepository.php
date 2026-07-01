<?php

declare(strict_types=1);

namespace Prestoworld\MarketplaceSdk;

use PrestoWorld\Contracts\Plugin\PluginRepositoryInterface;
use Prestoworld\MarketplaceSdk\Models\MarketplaceItem;

class PrestoWorldRepository implements PluginRepositoryInterface
{
    private MarketplaceClient $client;

    private array $config = [];

    public function __construct(?MarketplaceClient $client = null)
    {
        $this->client = $client ?? new MarketplaceClient();
    }

    public function getName(): string
    {
        return 'prestoworld';
    }

    public function getLabel(): string
    {
        return 'PrestoWorld Marketplace';
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;

        if (isset($config['base_url'])) {
            $this->client->setBaseUrl($config['base_url']);
        }
        if (isset($config['api_key'])) {
            $this->client->setApiKey($config['api_key']);
        }
    }

    /**
     * Discover available plugins/themes/extensions from the marketplace.
     *
     * @return array<MarketplaceItem>
     */
    public function discover(): array
    {
        $type = $this->config['type'] ?? 'plugin';
        $result = $this->client->browse([
            'type' => $type,
            'per_page' => $this->config['per_page'] ?? 100,
        ]);

        return array_map(
            fn(array $item) => MarketplaceItem::fromArray($item),
            $result['data'] ?? []
        );
    }

    /**
     * Fetch (download) a plugin by name and version.
     * Returns the download URL string.
     */
    public function fetch(string $pluginName, string $version): ?string
    {
        return $this->client->getDownloadUrl($pluginName, $version);
    }

    /**
     * Check if an update is available.
     * Returns the latest version string if an update exists, null otherwise.
     */
    public function hasUpdate(string $pluginName, string $currentVersion): ?string
    {
        $updates = $this->client->checkUpdates([
            ['slug' => $pluginName, 'current_version' => $currentVersion],
        ]);

        foreach ($updates as $update) {
            if ($update['slug'] === $pluginName) {
                return $update['latest_version'];
            }
        }

        return null;
    }

    /**
     * Get detailed plugin information.
     *
     * @return array|null
     */
    public function getPluginInfo(string $pluginName): ?array
    {
        $item = $this->client->getItem($pluginName);
        if ($item === null) {
            return null;
        }

        return MarketplaceItem::fromArray($item)->toArray();
    }
}
