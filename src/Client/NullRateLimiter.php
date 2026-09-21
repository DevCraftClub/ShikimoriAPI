<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Client;

final class NullRateLimiter implements RateLimiterInterface
{
    public function acquire(): void
    {
    }
}
