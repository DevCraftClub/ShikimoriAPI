<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\ContestDTO;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use DevCraftClub\Shikimori\Filter\ContestListFilter;
use DevCraftClub\Shikimori\Query\ContestQuery;

final readonly class ContestRepository
{
    public function __construct(
        private ShikimoriGraphQLClient $client
    ) {
    }

    /**
     * @return list<ContestDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function search(ContestListFilter $filter): array
    {
        $variables = $filter->toGraphQLVariables();
        $variableNames = array_keys($variables);

        $response = $this->client->query(
            ContestQuery::search($variableNames),
            $variables
        );

        /** @var list<array<string, mixed>> $contests */
        $contests = $response['contests'] ?? [];

        return array_map(
            static fn (array $contest): ContestDTO => ContestDTO::fromArray($contest),
            $contests
        );
    }
}
