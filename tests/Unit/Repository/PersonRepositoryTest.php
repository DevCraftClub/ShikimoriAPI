<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\DTO\PersonDTO;
use DevCraftClub\Shikimori\Filter\PersonListFilter;
use DevCraftClub\Shikimori\Repository\PersonRepository;
use GuzzleHttp\Psr7\Response;

/**
 * @covers \DevCraftClub\Shikimori\Repository\PersonRepository
 */
final class PersonRepositoryTest extends RepositoryTestCase
{
    public function testFindByIdReturnsDto(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['people' => [[
                    'id' => 1,
                    'name' => 'Hayao Miyazaki',
                    'russian' => null,
                    'japanese' => null,
                    'description' => null,
                    'url' => '/people/1',
                    'poster' => null,
                    'createdAt' => null,
                    'updatedAt' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new PersonRepository($client);
        $person = $repository->findById(1);

        self::assertInstanceOf(PersonDTO::class, $person);
        self::assertSame('Hayao Miyazaki', $person->getName());
    }

    public function testSearchReturnsDtos(): void
    {
        $client = $this->createClient();
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => ['people' => [[
                    'id' => 2,
                    'name' => 'Yoko Kanno',
                    'russian' => null,
                    'japanese' => null,
                    'description' => null,
                    'url' => '/people/2',
                    'poster' => null,
                    'createdAt' => null,
                    'updatedAt' => null,
                ]]],
            ], JSON_THROW_ON_ERROR)));

        $repository = new PersonRepository($client);
        $filter = (new PersonListFilter())->withSearch('Kanno')->withLimit(1);
        $results = $repository->search($filter);

        self::assertCount(1, $results);
        self::assertSame('Yoko Kanno', $results[0]->getName());
    }
}
