<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\CharacterDTO;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use DevCraftClub\Shikimori\Filter\CharacterListFilter;
use DevCraftClub\Shikimori\Query\CharacterQuery;

final readonly class CharacterRepository
{
    public function __construct(
        private ShikimoriGraphQLClient $client
    ) {
    }

    /**
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function findById(int $id): ?CharacterDTO
    {
        $response = $this->client->query(
            CharacterQuery::findById(),
            ['id' => (string) $id]
        );

        /** @var list<array<string, mixed>> $characters */
        $characters = $response['characters'] ?? [];
        if ($characters === []) {
            return null;
        }

        return CharacterDTO::fromArray($characters[0]);
    }

    /**
     * @return list<CharacterDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function search(CharacterListFilter $filter): array
    {
        $variables = $filter->toGraphQLVariables();
        $variableNames = array_keys($variables);

        $response = $this->client->query(
            CharacterQuery::search($variableNames),
            $variables
        );

        /** @var list<array<string, mixed>> $characters */
        $characters = $response['characters'] ?? [];

        return array_map(
            static fn (array $character): CharacterDTO => CharacterDTO::fromArray($character),
            $characters
        );
    }
}
