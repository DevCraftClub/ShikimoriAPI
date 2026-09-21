<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\DTO\UserRateDTO;
use DevCraftClub\Shikimori\Enum\UserRateStatus;
use DevCraftClub\Shikimori\Enum\UserRateTargetType;
use DevCraftClub\Shikimori\Filter\UserRateListFilter;
use DevCraftClub\Shikimori\Repository\UserRateRepository;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \DevCraftClub\Shikimori\Repository\UserRateRepository
 */
final class UserRateRepositoryTest extends RepositoryTestCase
{
    public function testListReturnsDtos(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['userRates' => [[
                    'id' => 1,
                    'score' => 10,
                    'status' => 'completed',
                    'text' => null,
                    'episodes' => 12,
                    'chapters' => null,
                    'rewatches' => 0,
                    'createdAt' => null,
                    'updatedAt' => null,
                    'user' => [
                        'id' => 1,
                        'nickname' => 'me',
                        'avatar' => null,
                    ],
                    'target' => [
                        'id' => 21,
                        'name' => 'One Piece',
                        'russian' => null,
                        'kind' => 'tv',
                        'status' => 'ongoing',
                        'score' => 8.5,
                        'episodes' => 0,
                        'episodesAired' => 1000,
                        'url' => '/animes/21-one-piece',
                        'poster' => null,
                    ],
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new UserRateRepository($client);
        $filter = (new UserRateListFilter())
            ->withUserId(1)
            ->withTargetType(UserRateTargetType::Anime)
            ->withStatus(UserRateStatus::Completed);
        $results = $repository->list($filter);

        self::assertCount(1, $results);
        self::assertInstanceOf(UserRateDTO::class, $results[0]);
        self::assertSame(10, $results[0]->getScore());
    }
}
