<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Tests\Unit\Filter;

use DevCraftClub\Shikimori\Enum\AnimeOrder;
use DevCraftClub\Shikimori\Filter\AnimeListFilter;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DevCraftClub\Shikimori\Filter\AnimeListFilter
 */
final class AnimeListFilterTest extends TestCase
{
    public function testFluentBuildAndVariables(): void
    {
        $filter = (new AnimeListFilter())
            ->withSearch('Naruto')
            ->withLimit(10)
            ->withOrder(AnimeOrder::Popularity)
            ->withIdsItem(1)
            ->withIdsItem('2');

        $variables = $filter->toGraphQLVariables();

        self::assertSame('Naruto', $variables['search']);
        self::assertSame(10, $variables['limit']);
        self::assertSame('popularity', $variables['order']);
        self::assertSame('1,2', $variables['ids']);
        self::assertSame(1, $variables['page']);
    }

    public function testLimitCappedAtFifty(): void
    {
        $filter = (new AnimeListFilter())->withLimit(100);

        self::assertSame(50, $filter->toGraphQLVariables()['limit']);
    }
}
