<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Persistence;

use DateTimeImmutable;
use Devcraft\Cache\FileCachePool;
use DevCraftClub\Shikimori\Config\SdkConfig;
use Psr\Cache\CacheItemPoolInterface;

final class PersistenceEngine
{
    private readonly ?CacheItemPoolInterface $cachePool;

    public function __construct(
        private readonly SdkConfig $config,
        private readonly ?EntityStoreInterface $entityStore = null,
        ?CacheItemPoolInterface $cachePool = null
    ) {
        $this->cachePool = $cachePool ?? ($config->getCacheDir() !== ''
            ? new FileCachePool($config->getCacheDir(), $config->getCacheTtl())
            : null);
    }

    /**
     * @param class-string<StorableEntity> $entityClass
     */
    public function find(
        string $type,
        int|string $id,
        string $profile,
        string $entityClass,
        bool $forceRecheck = false
    ): ?StorableEntity {
        if (!$forceRecheck && $this->entityStore !== null) {
            $entity = $this->entityStore->findById($entityClass, $id);
            if ($entity !== null && $this->isFresh($entity)) {
                return $entity;
            }
        }

        if (!$forceRecheck && $this->cachePool !== null) {
            $item = $this->cachePool->getItem($this->cacheKey($type, $id, $profile));
            if ($item->isHit()) {
                /** @var array<string, mixed>|null $data */
                $data = $item->get();
                if ($data !== null) {
                    return $entityClass::createFromArray($data);
                }
            }
        }

        return null;
    }

    /**
     * @param class-string<StorableEntity> $entityClass
     * @param array<string, mixed> $apiData
     */
    public function store(
        string $type,
        int|string $id,
        string $profile,
        string $entityClass,
        array $apiData
    ): StorableEntity {
        $entity = $entityClass::createFromArray($apiData);

        if ($this->entityStore !== null) {
            $existing = $this->entityStore->findById($entityClass, $id);
            if ($existing !== null) {
                $existing->updateFromArray($apiData);
                $existing->markFetchedNow();
                $this->entityStore->persist($existing);

                return $existing;
            }

            $this->entityStore->persist($entity);
        }

        if ($this->cachePool !== null) {
            $item = $this->cachePool->getItem($this->cacheKey($type, $id, $profile));
            $item->set($entity->toArray());
            $this->cachePool->save($item);
        }

        return $entity;
    }

    private function cacheKey(string $type, int|string $id, string $profile): string
    {
        return sprintf('shikimori/%s/%s/%s', $type, $id, $profile);
    }

    private function isFresh(StorableEntity $entity): bool
    {
        return (new DateTimeImmutable())->getTimestamp() - $entity->getFetchedAt()->getTimestamp() < $this->config->getCacheTtl();
    }
}
