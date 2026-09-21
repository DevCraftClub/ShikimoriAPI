<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Query;

final class ContestQuery
{
    private const CONTEST_FIELDS = <<<'GRAPHQL'
id
title
description
url
startedOn
finishedOn
createdAt
updatedAt
GRAPHQL;

    /**
     * @param list<string> $variableNames
     */
    public static function search(array $variableNames): string
    {
        $signature = self::buildSignature($variableNames);
        $arguments = self::buildArguments($variableNames);

        return 'query SearchContests' . $signature . ' {' . "\n"
            . '    contests' . $arguments . ' {' . "\n"
            . '        ' . self::CONTEST_FIELDS . "\n"
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
