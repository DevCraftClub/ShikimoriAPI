<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Enum;

enum AnimeOrder: string
{
    case Id = 'id';
    case Ranked = 'ranked';
    case Kind = 'kind';
    case Popularity = 'popularity';
    case Name = 'name';
    case AiredOn = 'aired_on';
    case Episodes = 'episodes';
    case Status = 'status';
}
