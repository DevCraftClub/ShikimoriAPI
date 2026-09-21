<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Config;

use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DevCraftClub\Shikimori\Config\SdkConfig
 */
final class SdkConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (array_keys($_ENV) as $key) {
            if (str_starts_with((string) $key, 'SHIKIMORI_')) {
                unset($_ENV[$key]);
            }
        }
    }

    public function testRequiresUserAgent(): void
    {
        $this->expectException(ConfigurationException::class);

        SdkConfig::fromEnv();
    }

    public function testDefaultsAndOverrides(): void
    {
        $_ENV['SHIKIMORI_USER_AGENT'] = 'Test/1.0';

        $config = SdkConfig::fromEnv([
            'cache_ttl' => 3600,
            'rate_limit_rps' => 2,
        ]);

        self::assertSame('Test/1.0', $config->getUserAgent());
        self::assertSame('https://shikimori.io/api/graphql', $config->getEndpoint());
        self::assertSame(3600, $config->getCacheTtl());
        self::assertSame(2, $config->getRateLimitRps());
        self::assertSame(90, $config->getRateLimitRpm());
        self::assertSame(sys_get_temp_dir() . '/shikimori_cache', $config->getCacheDir());
        self::assertSame('sqlite', $config->getDbDriver());
        self::assertSame('/tmp/shikimori.sqlite', $config->getDbSqlitePath());
    }

    public function testDatabaseEnvOverrides(): void
    {
        $_ENV['SHIKIMORI_USER_AGENT'] = 'Test/1.0';
        $_ENV['SHIKIMORI_DB_DRIVER'] = 'postgres';
        $_ENV['SHIKIMORI_DB_HOST'] = 'db.example.com';
        $_ENV['SHIKIMORI_DB_PORT'] = '5433';
        $_ENV['SHIKIMORI_DB_NAME'] = 'shiki';
        $_ENV['SHIKIMORI_DB_USER'] = 'shiki_user';
        $_ENV['SHIKIMORI_DB_PASSWORD'] = 'secret';

        $config = SdkConfig::fromEnv();

        self::assertSame('postgres', $config->getDbDriver());
        self::assertSame('db.example.com', $config->getDbHost());
        self::assertSame(5433, $config->getDbPort());
        self::assertSame('shiki', $config->getDbName());
        self::assertSame('shiki_user', $config->getDbUser());
        self::assertSame('secret', $config->getDbPassword());
    }

    public function testInvalidTtlThrows(): void
    {
        $_ENV['SHIKIMORI_USER_AGENT'] = 'Test/1.0';

        $this->expectException(ConfigurationException::class);

        SdkConfig::fromEnv(['cache_ttl' => 0]);
    }
}
