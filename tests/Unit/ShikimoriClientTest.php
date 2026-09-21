<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit;

use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Repository\AnimeRepository;
use DevCraftClub\Shikimori\Repository\MangaRepository;
use DevCraftClub\Shikimori\ShikimoriClient;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DevCraftClub\Shikimori\ShikimoriClient
 */
final class ShikimoriClientTest extends TestCase
{
    public function testExposesRepositories(): void
    {
        $config = SdkConfig::fromEnv(['user_agent' => 'Test/1.0', 'cache_dir' => '']);
        $client = new ShikimoriClient($config);

        self::assertInstanceOf(AnimeRepository::class, $client->animes());
        self::assertInstanceOf(MangaRepository::class, $client->mangas());
        self::assertSame($config, $client->getConfig());
    }
}
