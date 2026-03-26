<?php

namespace Prestoworld\MarketplaceSdk\Common;

use Prestoworld\MarketplaceSdk\Contracts\RepositoryItemInterface;

/**
 * Class MarketplaceExtension
 * 
 * Factory-based Data Transfer Object (DTO) that implements 
 * RepositoryItemInterface.
 */
class MarketplaceExtension implements RepositoryItemInterface
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getSlug(): string { return $this->data['slug'] ?? ''; }
    public function getType(): string { return $this->data['type'] ?? 'plugin'; }
    public function getName(): string { return $this->data['name'] ?? 'Untitled'; }
    public function getVersion(): string { return $this->data['latest_version'] ?? ($this->data['version'] ?? '1.0.0'); }
    public function getAuthor(): array { return $this->data['author'] ?? ['name' => 'PrestoWorld']; }
    public function getDescription(): string { return $this->data['description'] ?? ''; }
    public function getIcon(): array { return ['svg' => $this->data['icon_svg'] ?? '', 'color' => $this->data['icon_color'] ?? '#6366f1']; }
    public function getStats(): array { return ['installs' => $this->data['install_count'] ?? 0, 'rating' => $this->data['rating'] ?? 0]; }
    public function isPremium(): bool { return (bool) ($this->data['is_premium'] ?? false); }

    public function resolve(): array
    {
        return array_merge($this->data, [
            'id'           => $this->getSlug(),
            'name'         => $this->getName(),
            'author'       => $this->getAuthor(),
            'description'  => $this->getDescription(),
            'icon'         => $this->getIcon(),
            'stats'        => $this->getStats(),
            'is_premium'   => $this->isPremium(),
            'version'      => $this->getVersion(),
        ]);
    }
}
