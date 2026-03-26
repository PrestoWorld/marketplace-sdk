<?php

namespace Prestoworld\MarketplaceSdk\Platform;

use Prestoworld\MarketplaceSdk\Contracts\RepositoryProviderInterface;

/**
 * Class HubEngine
 * 
 * Pattern: High-level Platform Engine that uses any Strategy 
 * (ExtensionProvider) to serve themes and plugins over JSON.
 */
class HubEngine
{
    protected RepositoryProviderInterface $dataSource;

    public function __construct(RepositoryProviderInterface $source)
    {
        $this->dataSource = $source;
    }

    /**
     * Map a search request to the PrestoWorld JSON spec.
     */
    public function getCatalog(array $filters): array
    {
        $items = $this->dataSource->fetchAll($filters);
        
        return [
            'data' => array_map(fn($i) => $i->resolve(), $items),
            'pagination' => [
                'total' => $this->dataSource->count($filters),
                'page'  => (int)($filters['page'] ?? 1),
                'per_page' => 12,
            ],
        ];
    }

    /**
     * Detail API for a single item.
     */
    public function getInfo(string $slug, string $type = 'any'): array
    {
        $item = $this->dataSource->findBySlug($slug, $type);
        
        if (!$item) {
            return ['error' => 'Resource not found', 'code' => 404];
        }

        return $item->resolve();
    }
}
