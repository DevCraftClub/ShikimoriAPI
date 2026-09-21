<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Exception;

use RuntimeException;

final class AuthException extends RuntimeException implements ShikimoriException
{
    public static function missingClientCredentials(): self
    {
        return new self('OAuth client id and secret are required.');
    }

    public static function missingRefreshToken(): self
    {
        return new self('Refresh token is required for token refresh.');
    }

    public static function invalidTokenResponse(string $reason): self
    {
        return new self('Invalid OAuth token response: ' . $reason);
    }
}
