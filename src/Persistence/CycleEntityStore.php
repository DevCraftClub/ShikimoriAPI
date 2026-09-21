<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Persistence;

use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\RepositoryInterface;

final readonly class CycleEntityStore implements EntityStoreInterface
{
    public function __construct(
        private ORMInterface $orm,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function findById(string $entityClass, int|string $id): ?StorableEntity
    {
        $repository = $this->orm->getRepository($entityClass);
        $entity = $repository->findByPK($id);

        return $entity instanceof StorableEntity ? $entity : null;
    }

    public function persist(StorableEntity $entity): void
    {
        $entity->markFetchedNow();
        $this->entityManager->persist($entity)->run();
    }
}
