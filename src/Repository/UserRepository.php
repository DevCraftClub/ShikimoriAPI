<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\UserDTO;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;
use DevCraftClub\Shikimori\Filter\UserListFilter;
use DevCraftClub\Shikimori\Query\UserQuery;

final readonly class UserRepository
{
    public function __construct(
        private ShikimoriGraphQLClient $client
    ) {
    }

    /**
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function findById(int $id): ?UserDTO
    {
        $response = $this->client->query(
            UserQuery::findById(),
            ['id' => (string) $id]
        );

        /** @var list<array<string, mixed>> $users */
        $users = $response['users'] ?? [];
        if ($users === []) {
            return null;
        }

        return UserDTO::fromArray($users[0]);
    }

    /**
     * @return list<UserDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function search(UserListFilter $filter): array
    {
        $variables = $filter->toGraphQLVariables();
        $variableNames = array_keys($variables);

        $response = $this->client->query(
            UserQuery::search($variableNames),
            $variables
        );

        /** @var list<array<string, mixed>> $users */
        $users = $response['users'] ?? [];

        return array_map(
            static fn (array $user): UserDTO => UserDTO::fromArray($user),
            $users
        );
    }

    /**
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function currentUser(): ?UserDTO
    {
        $response = $this->client->query(UserQuery::currentUser());

        $data = $response['currentUser'] ?? null;
        if (!\is_array($data)) {
            return null;
        }

        return UserDTO::fromArray($data);
    }
}
