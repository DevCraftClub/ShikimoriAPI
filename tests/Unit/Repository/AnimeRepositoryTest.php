<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Repository;

use DevCraftClub\Shikimori\Client\ShikimoriGraphQLClient;
use DevCraftClub\Shikimori\Config\SdkConfig;
use DevCraftClub\Shikimori\DTO\AnimeDTO;
use DevCraftClub\Shikimori\Filter\AnimeListFilter;
use DevCraftClub\Shikimori\Persistence\PersistenceEngine;
use DevCraftClub\Shikimori\Query\Profile;
use DevCraftClub\Shikimori\Repository\AnimeRepository;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

/**
 * @covers \DevCraftClub\Shikimori\Repository\AnimeRepository
 */
final class AnimeRepositoryTest extends TestCase
{
    /** @var ClientInterface&MockObject */
    private ClientInterface $httpClient;
    private ShikimoriGraphQLClient $client;
    private PersistenceEngine $persistence;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(ClientInterface::class);
        $factory = new HttpFactory();
        $config = SdkConfig::fromEnv(['user_agent' => 'Test/1.0', 'cache_dir' => '/tmp/anime-test-cache']);
        $this->client = new ShikimoriGraphQLClient(
            $this->httpClient,
            $factory,
            $factory,
            $config
        );
        $this->persistence = new PersistenceEngine($config->withCacheDir(''));
    }

    public function testFindByIdReturnsDtoFromApi(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => [
                    'animes' => [[
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
                    ]],
                ],
            ], JSON_THROW_ON_ERROR)));

        $repository = new AnimeRepository($this->client, $this->persistence);
        $anime = $repository->findById(21);

        self::assertInstanceOf(AnimeDTO::class, $anime);
        self::assertSame(21, $anime->getId());
        self::assertSame('One Piece', $anime->getName());
    }

    public function testSearchReturnsDtosFromApi(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new Response(200, [], json_encode([
                'data' => [
                    'animes' => [[
                        'id' => 1,
                        'name' => 'Naruto',
                        'russian' => null,
                        'kind' => 'tv',
                        'status' => 'released',
                        'score' => 7.5,
                        'episodes' => 220,
                        'episodesAired' => 220,
                        'url' => '/animes/1-naruto',
                        'poster' => null,
                    ]],
                ],
            ], JSON_THROW_ON_ERROR)));

        $repository = new AnimeRepository($this->client, $this->persistence);
        $filter = (new AnimeListFilter())->withSearch('Naruto')->withLimit(1);
        $results = $repository->search($filter, Profile::Summary);

        self::assertCount(1, $results);
        self::assertSame('Naruto', $results[0]->getName());
    }
}
