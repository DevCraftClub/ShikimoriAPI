<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Exception;

use RuntimeException;

final class NetworkException extends RuntimeException implements ShikimoriException
{
    public static function wrap(\Throwable $previous): self
    {
        return new self(
            'Failed to execute Shikimori GraphQL request: ' . $previous->getMessage(),
            0,
            $previous
        );
    }
}
