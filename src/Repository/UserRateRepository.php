<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\UserRateDTO;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use DevCraftClub\Shikimori\Filter\UserRateListFilter;
use DevCraftClub\Shikimori\Query\UserRateQuery;

final readonly class UserRateRepository
{
    public function __construct(
        private ShikimoriGraphQLClient $client
    ) {
    }

    /**
     * @return list<UserRateDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function list(UserRateListFilter $filter): array
    {
        $variables = $filter->toGraphQLVariables();
        $variableNames = array_keys($variables);

        $response = $this->client->query(
            UserRateQuery::list($variableNames),
            $variables
        );

        /** @var list<array<string, mixed>> $userRates */
        $userRates = $response['userRates'] ?? [];

        return array_map(
            static fn (array $userRate): UserRateDTO => UserRateDTO::fromArray($userRate),
            $userRates
        );
    }
}
