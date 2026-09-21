<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\DTO\ContestDTO;
use DevCraftClub\Shikimori\Filter\ContestListFilter;
use DevCraftClub\Shikimori\Repository\ContestRepository;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \DevCraftClub\Shikimori\Repository\ContestRepository
 */
final class ContestRepositoryTest extends RepositoryTestCase
{
    public function testSearchReturnsDtos(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['contests' => [[
                    'id' => 1,
                    'title' => 'Best OP',
                    'description' => null,
                    'url' => '/contests/1',
                    'startedOn' => null,
                    'finishedOn' => null,
                    'createdAt' => null,
                    'updatedAt' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new ContestRepository($client);
        $filter = (new ContestListFilter())->withSearch('OP')->withLimit(1);
        $results = $repository->search($filter);

        self::assertCount(1, $results);
        self::assertInstanceOf(ContestDTO::class, $results[0]);
        self::assertSame('Best OP', $results[0]->getTitle());
    }
}
