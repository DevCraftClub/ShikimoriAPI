<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\DTO\GenreDTO;
use DevCraftClub\Shikimori\Enum\GenreEntryType;
use DevCraftClub\Shikimori\Repository\GenreRepository;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \DevCraftClub\Shikimori\Repository\GenreRepository
 */
final class GenreRepositoryTest extends RepositoryTestCase
{
    public function testListReturnsDtos(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['genres' => [[
                    'id' => 1,
                    'name' => 'Action',
                    'russian' => 'Экшен',
                    'kind' => 'anime',
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new GenreRepository($client);
        $genres = $repository->list(GenreEntryType::Anime);

        self::assertCount(1, $genres);
        self::assertInstanceOf(GenreDTO::class, $genres[0]);
        self::assertSame('Action', $genres[0]->getName());
    }
}
