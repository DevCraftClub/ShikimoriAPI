<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\MangaDTO;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use DevCraftClub\Shikimori\Filter\MangaListFilter;
use DevCraftClub\Shikimori\Query\MangaQuery;
use DevCraftClub\Shikimori\Query\Profile;

final readonly class MangaRepository
{
    public function __construct(
        private ShikimoriGraphQLClient $client
    ) {
    }

    /**
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function findById(int $id, Profile $profile = Profile::Summary): ?MangaDTO
    {
        $response = $this->client->query(
            MangaQuery::findById($profile),
            ['id' => (string) $id]
        );

        /** @var list<array<string, mixed>> $mangas */
        $mangas = $response['mangas'] ?? [];
        if ($mangas === []) {
            return null;
        }

        return MangaDTO::fromArray($mangas[0]);
    }

    /**
     * @return list<MangaDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function search(MangaListFilter $filter, Profile $profile = Profile::Summary): array
    {
        $variables = $filter->toGraphQLVariables();
        $variableNames = array_keys($variables);

        $response = $this->client->query(
            MangaQuery::search($profile, $variableNames),
            $variables
        );

        /** @var list<array<string, mixed>> $mangas */
        $mangas = $response['mangas'] ?? [];

        return array_map(
            static fn (array $manga): MangaDTO => MangaDTO::fromArray($manga),
            $mangas
        );
    }
}
