<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Query;

final class UserRateQuery
{
    private const USER_RATE_FIELDS = <<<'GRAPHQL'
id
score
status
text
episodes
chapters
rewatches
createdAt
updatedAt
user {
    id
    nickname
    avatar
}
target {
    ... on Anime {
        id
        name
        russian
        kind
        status
        score
        episodes
        episodesAired
        url
        poster {
            id
            originalUrl
            mainUrl
        }
    }
    ... on Manga {
        id
        name
        russian
        kind
        status
        score
        volumes
        chapters
        url
        poster {
            id
            originalUrl
            mainUrl
        }
    }
}
GRAPHQL;

    /**
     * @param list<string> $variableNames
     */
    public static function list(array $variableNames): string
    {
        $signature = self::buildSignature($variableNames);
        $arguments = self::buildArguments($variableNames);

        return 'query ListUserRates' . $signature . ' {' . "\n"
            . '    userRates' . $arguments . ' {' . "\n"
            . '        ' . self::USER_RATE_FIELDS . "\n"
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
            'userId' => 'ID',
            'targetType' => 'UserRateTargetTypeEnum',
            'status' => 'UserRateStatusEnum',
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
