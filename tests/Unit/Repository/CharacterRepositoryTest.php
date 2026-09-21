<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\DTO\CharacterDTO;
use DevCraftClub\Shikimori\Filter\CharacterListFilter;
use DevCraftClub\Shikimori\Repository\CharacterRepository;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \DevCraftClub\Shikimori\Repository\CharacterRepository
 */
final class CharacterRepositoryTest extends RepositoryTestCase
{
    public function testFindByIdReturnsDto(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['characters' => [[
                    'id' => 1,
                    'name' => 'Lelouch Lamperouge',
                    'russian' => null,
                    'japanese' => null,
                    'description' => null,
                    'url' => '/characters/1',
                    'poster' => null,
                    'createdAt' => null,
                    'updatedAt' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new CharacterRepository($client);
        $character = $repository->findById(1);

        self::assertInstanceOf(CharacterDTO::class, $character);
        self::assertSame('Lelouch Lamperouge', $character->getName());
    }

    public function testSearchReturnsDtos(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['characters' => [[
                    'id' => 2,
                    'name' => 'Spike Spiegel',
                    'russian' => null,
                    'japanese' => null,
                    'description' => null,
                    'url' => '/characters/2',
                    'poster' => null,
                    'createdAt' => null,
                    'updatedAt' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new CharacterRepository($client);
        $filter = (new CharacterListFilter())->withSearch('Spike')->withLimit(1);
        $results = $repository->search($filter);

        self::assertCount(1, $results);
        self::assertSame('Spike Spiegel', $results[0]->getName());
    }
}
