<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\AnimeDTO;
use DevCraftClub\Shikimori\Entity\AnimeEntity;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use DevCraftClub\Shikimori\Filter\AnimeListFilter;
use DevCraftClub\Shikimori\Persistence\PersistenceEngine;
use DevCraftClub\Shikimori\Query\AnimeQuery;
use DevCraftClub\Shikimori\Query\Profile;

final readonly class AnimeRepository
{
    public function __construct(
        private ShikimoriGraphQLClient $client,
        private PersistenceEngine $persistence
    ) {
    }

    /**
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function findById(int $id, Profile $profile = Profile::Summary, bool $forceRecheck = false): ?AnimeDTO
    {
        $entity = $this->persistence->find('anime', $id, $profile->value, AnimeEntity::class, $forceRecheck);
        if ($entity instanceof AnimeEntity && !$forceRecheck) {
            return $entity->toDto();
        }

        $response = $this->client->query(
            AnimeQuery::findById($profile),
            ['id' => (string) $id]
        );

        /** @var list<array<string, mixed>> $animes */
        $animes = $response['animes'] ?? [];
        if ($animes === []) {
            return null;
        }

        $storedEntity = $this->persistence->store('anime', $id, $profile->value, AnimeEntity::class, $animes[0]);

        return $storedEntity instanceof AnimeEntity ? $storedEntity->toDto() : AnimeDTO::fromArray($animes[0]);
    }

    /**
     * @return list<AnimeDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function search(AnimeListFilter $filter, Profile $profile = Profile::Summary): array
    {
        $variables = $filter->toGraphQLVariables();
        $variableNames = array_keys($variables);

        $response = $this->client->query(
            AnimeQuery::search($profile, $variableNames),
            $variables
        );

        /** @var list<array<string, mixed>> $animes */
        $animes = $response['animes'] ?? [];

        return array_map(
            static fn (array $anime): AnimeDTO => AnimeDTO::fromArray($anime),
            $animes
        );
    }
}
