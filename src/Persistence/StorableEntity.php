<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Persistence;

use DateTimeImmutable;

interface StorableEntity
{
    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): static;

    /**
     * @param array<string, mixed> $data
     */
    public function updateFromArray(array $data): void;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;

    public function getId(): int|string;

    public function getFetchedAt(): DateTimeImmutable;

    public function markFetchedNow(): void;
}
