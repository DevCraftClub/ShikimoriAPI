<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Enum;

enum UserRateStatus: string
{
    case Planned = 'planned';
    case Watching = 'watching';
    case Rewatching = 'rewatching';
    case Completed = 'completed';
    case OnHold = 'on_hold';
    case Dropped = 'dropped';
}
