<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Persistence;

use DateTimeImmutable;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Persistence\EntityStoreInterface;
use DevCraftClub\Shikimori\Persistence\PersistenceEngine;
use DevCraftClub\Shikimori\Persistence\StorableEntity;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @covers \DevCraftClub\Shikimori\Persistence\PersistenceEngine
 */
final class PersistenceEngineTest extends TestCase
{
    /** @var EntityStoreInterface&MockObject */
    private EntityStoreInterface $entityStore;
    /** @var CacheItemPoolInterface&MockObject */
    private CacheItemPoolInterface $cachePool;
    private SdkConfig $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityStore = $this->createMock(EntityStoreInterface::class);
        $this->cachePool = $this->createMock(CacheItemPoolInterface::class);
        $this->config = SdkConfig::fromEnv([
            'user_agent' => 'Test/1.0',
            'cache_ttl' => 3600,
            'cache_dir' => '/tmp/test-cache',
        ]);
    }

    public function testFindReturnsEntityFromStoreWhenFresh(): void
    {
        $entity = $this->createEntity(1, time());

        $this->entityStore
            ->expects($this->once())
            ->method('findById')
            ->with(FakeEntity::class, 1)
            ->willReturn($entity);

        $engine = new PersistenceEngine($this->config, $this->entityStore);
        $found = $engine->find('fake', 1, 'summary', FakeEntity::class);

        self::assertSame($entity, $found);
    }

    public function testFindFallsBackToCacheWhenStoreMisses(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn(['id' => 1, 'fetchedAt' => time()]);

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->with('shikimori/fake/1/summary')
            ->willReturn($item);

        $engine = new PersistenceEngine($this->config, null, $this->cachePool);
        $found = $engine->find('fake', 1, 'summary', FakeEntity::class);

        self::assertInstanceOf(FakeEntity::class, $found);
        self::assertSame(1, $found->getId());
    }

    public function testFindReturnsNullWhenNothingAvailable(): void
    {
        $engine = new PersistenceEngine($this->config);
        $found = $engine->find('fake', 1, 'summary', FakeEntity::class);

        self::assertNull($found);
    }

    public function testForceRecheckSkipsStoreAndCache(): void
    {
        $entity = $this->createEntity(1, time());

        $this->entityStore
            ->expects($this->never())
            ->method('findById');
        $this->cachePool
            ->expects($this->never())
            ->method('getItem');

        $engine = new PersistenceEngine($this->config, $this->entityStore, $this->cachePool);
        $found = $engine->find('fake', 1, 'summary', FakeEntity::class, true);

        self::assertNull($found);
    }

    public function testStoreCreatesEntityAndSavesToCache(): void
    {
        $item = $this->createMock(CacheItemInterface::class);
        $item->expects($this->once())->method('set')->with($this->anything());

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->willReturn($item);
        $this->cachePool
            ->expects($this->once())
            ->method('save')
            ->with($item);

        $engine = new PersistenceEngine($this->config, null, $this->cachePool);
        $entity = $engine->store('fake', 1, 'summary', FakeEntity::class, ['id' => 1]);

        self::assertInstanceOf(FakeEntity::class, $entity);
    }

    private function createEntity(int $id, int $fetchedAt): StorableEntity
    {
        return FakeEntity::createFromArray(['id' => $id, 'fetchedAt' => $fetchedAt]);
    }
}
