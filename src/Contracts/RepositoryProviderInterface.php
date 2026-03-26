<?php

namespace Prestoworld\MarketplaceSdk\Contracts;

/**
 * Interface RepositoryProviderInterface
 * 
 * Strategy Pattern: Allows the Hub platform to connect to 
 * different data sources (SQL, GitHub, etc.) to list items.
 */
interface RepositoryProviderInterface
{
    /**
     * Unique provider ID for this source.
     */
    public function getProviderId(): string;

    /**
     * List all plugins/themes from the source.
     * @return RepositoryItemInterface[]
     */
    public function fetchAll(array $filters = []): array;

    /**
     * Get a specific extension by slug.
     */
    public function findBySlug(string $slug, string $type = 'any'): ?RepositoryItemInterface;

    /**
     * Total items for pagination.
     */
    public function count(array $filters = []): int;
}
