<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Persistence;

use Cycle\Database\Config\DatabaseConfig as CycleDatabaseConfig;
use Cycle\Database\Config\MySQL\TcpConnectionConfig as MysqlTcp;
use Cycle\Database\Config\MySQLDriverConfig;
use Cycle\Database\Config\Postgres\TcpConnectionConfig as PostgresTcp;
use Cycle\Database\Config\PostgresDriverConfig;
use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\Factory;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Select\Repository;
use Cycle\ORM\Select\Source;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Entity\AnimeEntity;
use DevCraftClub\Shikimori\Exception\ConfigurationException;

final class CycleOrmFactory
{
    /**
     * @return array{0: ORMInterface, 1: EntityManagerInterface}
     */
    public static function fromConfig(SdkConfig $config): array
    {
        $dbal = new DatabaseManager(self::buildDatabaseConfig($config));
        $orm = new ORM(new Factory($dbal), new Schema(self::buildSchema()));

        return [$orm, new EntityManager($orm)];
    }

    /**
     * @return array<string, array<int|string, mixed>>
     */
    private static function buildSchema(): array
    {
        $entity = AnimeEntity::class;

        return [
            $entity => [
                SchemaInterface::ENTITY => $entity,
                SchemaInterface::MAPPER => Mapper::class,
                SchemaInterface::SOURCE => Source::class,
                SchemaInterface::REPOSITORY => Repository::class,
                SchemaInterface::DATABASE => 'default',
                SchemaInterface::TABLE => 'shikimori_animes',
                SchemaInterface::PRIMARY_KEY => ['id'],
                SchemaInterface::FIND_BY_KEYS => ['id'],
                SchemaInterface::COLUMNS => [
                    'id' => 'id',
                    'name' => 'name',
                    'russian' => 'russian',
                    'kind' => 'kind',
                    'status' => 'status',
                    'score' => 'score',
                    'episodes' => 'episodes',
                    'episodesAired' => 'episodes_aired',
                    'description' => 'description',
                    'url' => 'url',
                    'duration' => 'duration',
                    'rating' => 'rating',
                    'franchise' => 'franchise',
                    'airedOn' => 'aired_on',
                    'releasedOn' => 'released_on',
                    'updatedAt' => 'updated_at',
                    'poster' => 'poster',
                    'genres' => 'genres',
                    'studios' => 'studios',
                    'fetchedAt' => 'fetched_at',
                ],
                SchemaInterface::RELATIONS => [],
                SchemaInterface::SCOPE => null,
                SchemaInterface::TYPECAST => [],
                SchemaInterface::SCHEMA => [],
                SchemaInterface::TYPECAST_HANDLER => null,
                SchemaInterface::GENERATED_FIELDS => ['id' => 2],
            ],
        ];
    }

    private static function buildDatabaseConfig(SdkConfig $config): CycleDatabaseConfig
    {
        $driver = strtolower($config->getDbDriver());

        return match ($driver) {
            'sqlite' => self::buildSqliteConfig($config),
            'postgres', 'postgresql' => self::buildPostgresConfig($config),
            'mysql' => self::buildMysqlConfig($config),
            default => throw ConfigurationException::unsupportedDbDriver($driver),
        };
    }

    private static function buildSqliteConfig(SdkConfig $config): CycleDatabaseConfig
    {
        $path = $config->getDbSqlitePath();
        $connection = $path === null || $path === ''
            ? new MemoryConnectionConfig()
            : new FileConnectionConfig($path);

        return new CycleDatabaseConfig([
            'default' => 'default',
            'databases' => ['default' => ['connection' => 'default']],
            'connections' => ['default' => new SQLiteDriverConfig($connection)],
        ]);
    }

    private static function buildPostgresConfig(SdkConfig $config): CycleDatabaseConfig
    {
        [$database, $host, $port, $user, $password] = self::resolveTcpCredentials($config);

        $connection = new PostgresTcp(
            database: $database,
            host: $host,
            port: $port,
            user: $user,
            password: $password,
        );

        return new CycleDatabaseConfig([
            'default' => 'default',
            'databases' => ['default' => ['connection' => 'default']],
            'connections' => ['default' => new PostgresDriverConfig($connection)],
        ]);
    }

    private static function buildMysqlConfig(SdkConfig $config): CycleDatabaseConfig
    {
        [$database, $host, $port, $user, $password] = self::resolveTcpCredentials($config);

        $connection = new MysqlTcp(
            database: $database,
            host: $host,
            port: $port,
            user: $user,
            password: $password,
        );

        return new CycleDatabaseConfig([
            'default' => 'default',
            'databases' => ['default' => ['connection' => 'default']],
            'connections' => ['default' => new MySQLDriverConfig($connection)],
        ]);
    }

    /**
     * @return array{0: non-empty-string, 1: non-empty-string, 2: positive-int, 3: non-empty-string, 4: non-empty-string|null}
     */
    private static function resolveTcpCredentials(SdkConfig $config): array
    {
        self::requireDbCredentials($config);

        $database = $config->getDbName();
        $host = $config->getDbHost();
        $port = $config->getDbPort() ?? 5432;
        $user = $config->getDbUser();
        $password = $config->getDbPassword();

        \assert($database !== '');
        \assert($host !== '');
        \assert($port > 0);
        \assert($user !== null && $user !== '');

        $password = ($password === '' || $password === null) ? null : $password;

        return [$database, $host, $port, $user, $password];
    }

    private static function requireDbCredentials(SdkConfig $config): void
    {
        if ($config->getDbName() === '') {
            throw ConfigurationException::missingDbCredential('name');
        }

        if ($config->getDbUser() === null || $config->getDbUser() === '') {
            throw ConfigurationException::missingDbCredential('user');
        }
    }
}
