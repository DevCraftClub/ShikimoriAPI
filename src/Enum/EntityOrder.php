<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Enum;

enum EntityOrder: string
{
    case Id = 'id';
    case Ranked = 'ranked';
    case Kind = 'kind';
    case Popularity = 'popularity';
    case Name = 'name';
    case AiredOn = 'aired_on';
    case CreatedAt = 'created_at';
    case UpdatedAt = 'updated_at';
}
