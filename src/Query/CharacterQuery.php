<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Query;

final class CharacterQuery
{
    private const CHARACTER_FIELDS = <<<'GRAPHQL'
id
name
russian
japanese
description
url
poster {
  id
  originalUrl
  mainUrl
}
createdAt
updatedAt
GRAPHQL;

    public static function findById(): string
    {
        return 'query GetCharacter($id: String!) {' . "\n"
            . '    characters(ids: $id, limit: 1) {' . "\n"
            . '        ' . self::CHARACTER_FIELDS . "\n"
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

        return 'query SearchCharacters' . $signature . ' {' . "\n"
            . '    characters' . $arguments . ' {' . "\n"
            . '        ' . self::CHARACTER_FIELDS . "\n"
            . '    }' . "\n"
            . '}';
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
