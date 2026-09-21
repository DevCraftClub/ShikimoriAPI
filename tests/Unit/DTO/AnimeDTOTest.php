<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\DTO;

use DevCraftClub\Shikimori\DTO\AnimeDTO;
use DevCraftClub\Shikimori\DTO\GenreDTO;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DevCraftClub\Shikimori\DTO\AnimeDTO
 */
final class AnimeDTOTest extends TestCase
{
    public function testFromArrayAndToArray(): void
    {
        $anime = AnimeDTO::fromArray([
            'id' => 21,
            'name' => 'One Piece',
            'russian' => 'Ван Пис',
            'kind' => 'tv',
            'status' => 'ongoing',
            'score' => 8.5,
            'episodes' => 0,
            'episodesAired' => 1000,
            'url' => '/animes/21-one-piece',
            'poster' => ['id' => 'p1', 'mainUrl' => 'https://image'],
            'genres' => [
                ['id' => 1, 'name' => 'Action', 'russian' => 'Экшен', 'kind' => 'anime'],
            ],
            'studios' => [],
        ]);

        self::assertSame(21, $anime->getId());
        self::assertSame('One Piece', $anime->getName());
        self::assertSame('Ван Пис', $anime->getRussian());
        self::assertSame('https://image', $anime->getPoster()?->getMainUrl());
        self::assertCount(1, $anime->getGenres());
        self::assertInstanceOf(GenreDTO::class, $anime->getGenres()[0]);

        $array = $anime->toArray();
        self::assertSame('One Piece', $array['name']);
        self::assertIsArray($array['poster']);
        self::assertSame('https://image', $array['poster']['mainUrl'] ?? null);
    }
}
