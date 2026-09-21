<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Persistence;

interface EntityStoreInterface
{
    /**
     * @param class-string<StorableEntity> $entityClass
     */
    public function findById(string $entityClass, int|string $id): ?StorableEntity;

    public function persist(StorableEntity $entity): void;
}
