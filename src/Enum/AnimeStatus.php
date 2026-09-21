<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Enum;

enum AnimeStatus: string
{
    case Anons = 'anons';
    case Ongoing = 'ongoing';
    case Released = 'released';
}
