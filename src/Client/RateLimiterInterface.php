<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Client;

interface RateLimiterInterface
{
    public function acquire(): void;
}
