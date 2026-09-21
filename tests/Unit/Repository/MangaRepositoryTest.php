<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\DTO\MangaDTO;
use DevCraftClub\Shikimori\Filter\MangaListFilter;
use DevCraftClub\Shikimori\Repository\MangaRepository;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \DevCraftClub\Shikimori\Repository\MangaRepository
 */
final class MangaRepositoryTest extends RepositoryTestCase
{
    public function testFindByIdReturnsDto(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['mangas' => [[
                    'id' => 1,
                    'name' => 'Berserk',
                    'russian' => null,
                    'kind' => 'manga',
                    'status' => 'released',
                    'score' => 9.0,
                    'volumes' => 40,
                    'chapters' => 360,
                    'url' => '/mangas/1-berserk',
                    'poster' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new MangaRepository($client);
        $manga = $repository->findById(1);

        self::assertInstanceOf(MangaDTO::class, $manga);
        self::assertSame('Berserk', $manga->getName());
    }

    public function testSearchReturnsDtos(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['mangas' => [[
                    'id' => 2,
                    'name' => 'Naruto',
                    'russian' => null,
                    'kind' => 'manga',
                    'status' => 'released',
                    'score' => 7.5,
                    'volumes' => null,
                    'chapters' => 700,
                    'url' => '/mangas/2-naruto',
                    'poster' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new MangaRepository($client);
        $filter = (new MangaListFilter())->withSearch('Naruto')->withLimit(1);
        $results = $repository->search($filter);

        self::assertCount(1, $results);
        self::assertSame('Naruto', $results[0]->getName());
    }
}
