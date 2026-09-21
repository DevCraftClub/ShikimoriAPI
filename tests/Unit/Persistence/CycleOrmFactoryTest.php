<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Persistence;

use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\ConfigurationException;
use DevCraftClub\Shikimori\Persistence\CycleOrmFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DevCraftClub\Shikimori\Persistence\CycleOrmFactory
 */
final class CycleOrmFactoryTest extends TestCase
{
    public function testBuildsOrmAndEntityManagerFromSqliteMemoryConfig(): void
    {
        $config = (new SdkConfig())
            ->withUserAgent('Test/1.0')
            ->withDbDriver('sqlite')
            ->withDbSqlitePath('');

        [$orm, $entityManager] = @CycleOrmFactory::fromConfig($config);

        self::assertInstanceOf(ORMInterface::class, $orm);
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);
    }

    public function testUnsupportedDriverThrows(): void
    {
        $config = (new SdkConfig())
            ->withUserAgent('Test/1.0')
            ->withDbDriver('oracle');

        $this->expectException(ConfigurationException::class);

        CycleOrmFactory::fromConfig($config);
    }
}
