<?php

declare(strict_types=1);

namespace Prestoworld\MarketplaceSdk\Models;

class MarketplaceItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $name,
        public readonly string $itemType,
        public readonly string $description,
        public readonly ?string $latestVersion,
        public readonly string $authorName,
        public readonly bool $isPremium,
        public readonly ?string $iconSvg,
        public readonly string $iconColor,
        public readonly int $installCount,
        public readonly float $rating,
        public readonly string $category,
        public readonly ?string $tags,
        public readonly ?string $requires,
        public readonly ?string $testedUpTo,
        public readonly ?string $previewUrl,
        public readonly ?string $downloadUrl,
        public readonly string $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)($data['id'] ?? 0),
            slug: $data['slug'] ?? '',
            name: $data['name'] ?? '',
            itemType: $data['item_type'] ?? 'extension',
            description: $data['description'] ?? '',
            latestVersion: $data['latest_version'] ?? null,
            authorName: $data['author_name'] ?? 'Unknown',
            isPremium: (bool)($data['is_premium'] ?? false),
            iconSvg: $data['icon_svg'] ?? null,
            iconColor: $data['icon_color'] ?? '#6366f1',
            installCount: (int)($data['install_count'] ?? 0),
            rating: (float)($data['rating'] ?? 5.0),
            category: $data['category'] ?? 'Uncategorized',
            tags: $data['tags'] ?? null,
            requires: $data['requires'] ?? null,
            testedUpTo: $data['tested_up_to'] ?? null,
            previewUrl: $data['preview_url'] ?? null,
            downloadUrl: $data['download_url'] ?? null,
            createdAt: $data['created_at'] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'item_type' => $this->itemType,
            'description' => $this->description,
            'latest_version' => $this->latestVersion,
            'author_name' => $this->authorName,
            'is_premium' => $this->isPremium,
            'icon_svg' => $this->iconSvg,
            'icon_color' => $this->iconColor,
            'install_count' => $this->installCount,
            'rating' => $this->rating,
            'category' => $this->category,
            'tags' => $this->tags,
            'requires' => $this->requires,
            'tested_up_to' => $this->testedUpTo,
            'preview_url' => $this->previewUrl,
            'download_url' => $this->downloadUrl,
            'created_at' => $this->createdAt,
        ];
    }
}
