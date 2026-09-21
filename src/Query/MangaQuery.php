<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Query;

final class MangaQuery
{
    private const MANGA_FIELDS = <<<'GRAPHQL'
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
GRAPHQL;

    private const MANGA_DETAIL_FIELDS = <<<'GRAPHQL'
description
airedOn {
  year
  month
  day
  date
}
releasedOn {
  year
  month
  day
  date
}
updatedAt
genres {
  id
  name
  russian
  kind
}
GRAPHQL;

    public static function findById(Profile $profile): string
    {
        $fields = self::MANGA_FIELDS;
        if ($profile === Profile::Detail) {
            $fields .= "\n" . self::MANGA_DETAIL_FIELDS;
        }

        return <<<GRAPHQL
query GetManga(\$id: String!) {
    mangas(ids: \$id, limit: 1) {
        {$fields}
    }
}
GRAPHQL;
    }

    /**
     * @param list<string> $variableNames
     */
    public static function search(Profile $profile, array $variableNames): string
    {
        $fields = self::MANGA_FIELDS;
        if ($profile === Profile::Detail) {
            $fields .= "\n" . self::MANGA_DETAIL_FIELDS;
        }

        $signature = self::buildSignature($variableNames);
        $arguments = self::buildArguments($variableNames);

        return <<<GRAPHQL
query SearchMangas{$signature} {
    mangas{$arguments} {
        {$fields}
    }
}
GRAPHQL;
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
