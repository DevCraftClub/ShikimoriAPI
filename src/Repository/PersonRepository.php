<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\PersonDTO;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use DevCraftClub\Shikimori\Filter\PersonListFilter;
use DevCraftClub\Shikimori\Query\PersonQuery;

final readonly class PersonRepository
{
    public function __construct(
        private ShikimoriGraphQLClient $client
    ) {
    }

    /**
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function findById(int $id): ?PersonDTO
    {
        $response = $this->client->query(
            PersonQuery::findById(),
            ['id' => (string) $id]
        );

        /** @var list<array<string, mixed>> $people */
        $people = $response['people'] ?? [];
        if ($people === []) {
            return null;
        }

        return PersonDTO::fromArray($people[0]);
    }

    /**
     * @return list<PersonDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function search(PersonListFilter $filter): array
    {
        $variables = $filter->toGraphQLVariables();
        $variableNames = array_keys($variables);

        $response = $this->client->query(
            PersonQuery::search($variableNames),
            $variables
        );

        /** @var list<array<string, mixed>> $people */
        $people = $response['people'] ?? [];

        return array_map(
            static fn (array $person): PersonDTO => PersonDTO::fromArray($person),
            $people
        );
    }
}
