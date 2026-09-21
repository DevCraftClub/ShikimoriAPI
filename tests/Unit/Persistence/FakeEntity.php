<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Persistence;

use DateTimeImmutable;
use DevCraftClub\Shikimori\Persistence\StorableEntity;

final class FakeEntity implements StorableEntity
{
    private function __construct(
        private int $id,
        private DateTimeImmutable $fetchedAt
    ) {
    }

    public static function createFromArray(array $data): static
    {
        $fetchedAt = $data['fetchedAt'] ?? null;
        $id = isset($data['id']) && (\is_int($data['id']) || \is_string($data['id'])) ? (int) $data['id'] : 0;
        if ($fetchedAt instanceof DateTimeImmutable) {
            return new self($id, $fetchedAt);
        }

        $ts = \is_int($fetchedAt) || \is_string($fetchedAt) ? (int) $fetchedAt : time();

        return new self($id, new DateTimeImmutable('@' . $ts));
    }

    public function updateFromArray(array $data): void
    {
        $this->id = isset($data['id']) && (\is_int($data['id']) || \is_string($data['id'])) ? (int) $data['id'] : 0;
        $this->fetchedAt = new DateTimeImmutable();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fetchedAt' => $this->fetchedAt->getTimestamp(),
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFetchedAt(): DateTimeImmutable
    {
        return $this->fetchedAt;
    }

    public function markFetchedNow(): void
    {
        $this->fetchedAt = new DateTimeImmutable();
    }
}
