<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Persistence;

use Cycle\Annotated\Embeddings;
use Cycle\Annotated\Entities;
use Cycle\Annotated\MergeColumns;
use Cycle\Annotated\MergeIndexes;
use Cycle\Annotated\TableInheritance;
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
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator\GenerateModifiers;
use Cycle\Schema\Generator\GenerateRelations;
use Cycle\Schema\Generator\RenderTables;
use Cycle\Schema\Generator\ValidateEntities;
use Cycle\Schema\Registry;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Entity\AnimeEntity;
use DevCraftClub\Shikimori\Exception\ConfigurationException;

final class CycleOrmFactory
{
    /**
     * @param list<class-string> $entityClasses
     *
     * @return array{0: ORMInterface, 1: EntityManagerInterface}
     */
    public static function fromConfig(SdkConfig $config, array $entityClasses = [AnimeEntity::class]): array
    {
        $dbal = new DatabaseManager(self::buildDatabaseConfig($config));
        $locator = new EntityClassLocator($entityClasses);
        $registry = new Registry($dbal);

        $generators = [
            new Embeddings($locator),
            new Entities($locator),
            new TableInheritance(),
            new MergeColumns(),
            new GenerateRelations(),
            new GenerateModifiers(),
            new ValidateEntities(),
            new RenderTables(),
            new MergeIndexes(),
        ];

        $schema = (new Compiler())->compile($registry, $generators);
        $orm = new ORM(new Factory($dbal), new Schema($schema));

        return [$orm, new EntityManager($orm)];
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
