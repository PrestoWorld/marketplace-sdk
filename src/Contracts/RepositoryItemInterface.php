<?php

namespace Prestoworld\MarketplaceSdk\Contracts;

/**
 * Interface RepositoryItemInterface
 * 
 * Strategy Pattern: defines the base contract for Themes/Plugins
 * metadata regardless of the source.
 */
interface RepositoryItemInterface
{
    public function getSlug(): string;
    public function getType(): string; // plugin or theme
    public function getName(): string;
    public function getVersion(): string;
    public function getAuthor(): array;
    public function getDescription(): string;
    public function getIcon(): array; // ['svg' => '...', 'color' => '...']
    public function getStats(): array; // ['installs' => 123, 'rating' => 4.5]
    public function isPremium(): bool;
    
    /**
     * Resolve the cross-platform JSON spec.
     */
    public function resolve(): array;
}
