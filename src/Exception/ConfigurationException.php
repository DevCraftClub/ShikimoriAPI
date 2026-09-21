<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Exception;

use InvalidArgumentException;

final class ConfigurationException extends InvalidArgumentException implements ShikimoriException
{
    public static function missingUserAgent(): self
    {
        return new self('User-Agent is required. Set SHIKIMORI_USER_AGENT environment variable.');
    }

    public static function invalidPositive(string $name, int $value): self
    {
        return new self(sprintf('%s must be positive, got %d', $name, $value));
    }

    public static function unsupportedDbDriver(string $driver): self
    {
        return new self(sprintf('Unsupported database driver "%s". Use sqlite, postgres or mysql.', $driver));
    }

    public static function missingDbCredential(string $name): self
    {
        return new self(sprintf('Database %s is required for the selected driver.', $name));
    }
}
