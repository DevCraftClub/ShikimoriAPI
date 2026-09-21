<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Exception;

use RuntimeException;

final class GraphQLQueryException extends RuntimeException implements ShikimoriException
{
    /**
     * @param list<string> $messages
     */
    public static function fromMessages(array $messages): self
    {
        return new self('GraphQL Errors: ' . implode('; ', $messages));
    }
}
