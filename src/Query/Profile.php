<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Query;

enum Profile: string
{
    case Summary = 'summary';
    case Detail = 'detail';
}
