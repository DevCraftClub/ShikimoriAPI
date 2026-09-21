<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Query;

final class UserQuery
{
    private const USER_FIELDS = <<<'GRAPHQL'
id
nickname
avatar
lastOnlineAt
url
name
sex
fullYears
createdAt
GRAPHQL;

    private const CURRENT_USER_QUERY = 'query CurrentUser {' . "\n"
        . '    currentUser {' . "\n"
        . '        ' . self::USER_FIELDS . "\n"
        . '    }' . "\n"
        . '}';

    public static function findById(): string
    {
        return 'query GetUser($id: String!) {' . "\n"
            . '    users(ids: $id, limit: 1) {' . "\n"
            . '        ' . self::USER_FIELDS . "\n"
            . '    }' . "\n"
            . '}';
    }

    /**
     * @param list<string> $variableNames
     */
    public static function search(array $variableNames): string
    {
        $signature = self::buildSignature($variableNames);
        $arguments = self::buildArguments($variableNames);

        return 'query SearchUsers' . $signature . ' {' . "\n"
            . '    users' . $arguments . ' {' . "\n"
            . '        ' . self::USER_FIELDS . "\n"
            . '    }' . "\n"
            . '}';
    }

    public static function currentUser(): string
    {
        return self::CURRENT_USER_QUERY;
    }

    /**
     * @param list<string> $names
     */
    private static function buildSignature(array $names): string
    {
        $defs = [];
        $mapping = [
            'page' => 'Int',
            'limit' => 'Int',
            'search' => 'String',
            'ids' => 'String',
        ];

        foreach ($names as $name) {
            if (isset($mapping[$name])) {
                $defs[] = '$' . $name . ': ' . $mapping[$name];
            }
        }

        return $defs === [] ? '' : '(' . implode(', ', $defs) . ')';
    }

    /**
     * @param list<string> $names
     */
    private static function buildArguments(array $names): string
    {
        $args = [];
        foreach ($names as $name) {
            $args[] = $name . ': $' . $name;
        }

        return '(' . implode(', ', $args) . ')';
    }
}
