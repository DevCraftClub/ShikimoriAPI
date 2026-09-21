<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\DTO\GenreDTO;
use DevCraftClub\Shikimori\Enum\GenreEntryType;
use DevCraftClub\Shikimori\Exception\GraphQLQueryException;
use DevCraftClub\Shikimori\Exception\NetworkException;

final readonly class GenreRepository
{
    private const LIST_QUERY = <<<'GRAPHQL'
query Genres($entryType: GenreEntryTypeEnum!) {
    genres(entryType: $entryType) {
        id
        name
        russian
        kind
    }
}
GRAPHQL;

    public function __construct(
        private ShikimoriGraphQLClient $client
    ) {
    }

    /**
     * @return list<GenreDTO>
     *
     * @throws GraphQLQueryException
     * @throws NetworkException
     */
    public function list(GenreEntryType $entryType): array
    {
        $response = $this->client->query(
            self::LIST_QUERY,
            ['entryType' => $entryType->value]
        );

        /** @var list<array<string, mixed>> $genres */
        $genres = $response['genres'] ?? [];

        return array_map(
            static fn (array $genre): GenreDTO => GenreDTO::fromArray($genre),
            $genres
        );
    }
}
