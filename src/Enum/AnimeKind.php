<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Enum;

enum AnimeKind: string
{
    case Tv = 'tv';
    case Movie = 'movie';
    case Ova = 'ova';
    case Ona = 'ona';
    case Special = 'special';
    case Music = 'music';
    case TvSpecial = 'tv_special';
    case None = 'none';
}
