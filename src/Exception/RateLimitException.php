<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Exception;

use RuntimeException;

final class RateLimitException extends RuntimeException implements ShikimoriException
{
    public static function exceeded(int $retryAfter): self
    {
        return new self(
            sprintf('Shikimori rate limit exceeded. Retry after %d second(s).', $retryAfter),
            429
        );
    }
}
