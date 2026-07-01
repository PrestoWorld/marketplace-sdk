<?php

declare(strict_types=1);

namespace Prestoworld\MarketplaceSdk\Exceptions;

class MarketplaceException extends \RuntimeException
{
    public static function connectionFailed(string $message): self
    {
        return new self("Marketplace connection failed: {$message}");
    }

    public static function notFound(string $slug): self
    {
        return new self("Item '{$slug}' not found in marketplace");
    }

    public static function invalidResponse(string $detail): self
    {
        return new self("Invalid marketplace response: {$detail}");
    }
}
