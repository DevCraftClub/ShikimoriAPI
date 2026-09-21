<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Enum;

enum AnimeRating: string
{
    case None = 'none';
    case G = 'g';
    case Pg = 'pg';
    case Pg13 = 'pg_13';
    case R = 'r';
    case RPlus = 'r_plus';
    case Rx = 'rx';
}
