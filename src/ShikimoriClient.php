<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori;

use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use DevCraftClub\Shikimori\Auth\OAuth2Helper;
use DevCraftClub\Shikimori\Client\ClientFactory;
use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\Exception\ConfigurationException;
use DevCraftClub\Shikimori\Persistence\CycleEntityStore;
use DevCraftClub\Shikimori\Persistence\CycleOrmFactory;
use DevCraftClub\Shikimori\Persistence\EntityStoreInterface;
use DevCraftClub\Shikimori\Persistence\PersistenceEngine;
use DevCraftClub\Shikimori\Repository\AnimeRepository;
use DevCraftClub\Shikimori\Repository\CharacterRepository;
use DevCraftClub\Shikimori\Repository\ContestRepository;
use DevCraftClub\Shikimori\Repository\GenreRepository;
use DevCraftClub\Shikimori\Repository\MangaRepository;
use DevCraftClub\Shikimori\Repository\PersonRepository;
use DevCraftClub\Shikimori\Repository\UserRateRepository;
use DevCraftClub\Shikimori\Repository\UserRepository;

final class ShikimoriClient
{
    private ShikimoriGraphQLClient $graphQLClient;
    private PersistenceEngine $persistence;
    private ?EntityStoreInterface $entityStore = null;

    public function __construct(
        private readonly SdkConfig $config,
        ?ShikimoriGraphQLClient $graphQLClient = null,
        ?PersistenceEngine $persistence = null,
        ?EntityStoreInterface $entityStore = null
    ) {
        $this->graphQLClient = $graphQLClient ?? ClientFactory::fromConfig($config);
        $this->entityStore = $entityStore;
        $this->persistence = $persistence ?? new PersistenceEngine($config, $entityStore);
    }

    public static function fromEnv(): self
    {
        return new self(SdkConfig::fromEnv());
    }

    public function withOrm(ORMInterface $orm, EntityManagerInterface $entityManager): self
    {
        $store = new CycleEntityStore($orm, $entityManager);

        return new self(
            $this->config,
            $this->graphQLClient,
            new PersistenceEngine($this->config, $store),
            $store
        );
    }

    public function withDatabase(): self
    {
        [$orm, $entityManager] = CycleOrmFactory::fromConfig($this->config);

        return $this->withOrm($orm, $entityManager);
    }

    public function getConfig(): SdkConfig
    {
        return $this->config;
    }

    public function getGraphQLClient(): ShikimoriGraphQLClient
    {
        return $this->graphQLClient;
    }

    public function getEntityStore(): ?EntityStoreInterface
    {
        return $this->entityStore;
    }

    public function oauth(): OAuth2Helper
    {
        return OAuth2Helper::fromConfig($this->config);
    }

    public function animes(): AnimeRepository
    {
        return new AnimeRepository($this->graphQLClient, $this->persistence);
    }

    public function mangas(): MangaRepository
    {
        return new MangaRepository($this->graphQLClient);
    }

    public function characters(): CharacterRepository
    {
        return new CharacterRepository($this->graphQLClient);
    }

    public function people(): PersonRepository
    {
        return new PersonRepository($this->graphQLClient);
    }

    public function genres(): GenreRepository
    {
        return new GenreRepository($this->graphQLClient);
    }

    public function users(): UserRepository
    {
        return new UserRepository($this->graphQLClient);
    }

    public function userRates(): UserRateRepository
    {
        return new UserRateRepository($this->graphQLClient);
    }

    public function contests(): ContestRepository
    {
        return new ContestRepository($this->graphQLClient);
    }
}
