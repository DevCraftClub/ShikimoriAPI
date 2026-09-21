<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Util;

use DateTimeImmutable;

final class ArrayUtil
{
    /**
     * @param array<string, mixed> $data
     */
    public static function stringOrDefault(array $data, string $key, string $default): string
    {
        return \is_string($data[$key] ?? null) ? (string) $data[$key] : $default;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function optionalString(array $data, string $key): ?string
    {
        return \is_string($data[$key] ?? null) ? (string) $data[$key] : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function optionalInt(array $data, string $key): ?int
    {
        return \is_int($data[$key] ?? null) ? (int) $data[$key] : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function optionalFloat(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return \is_int($value) || \is_float($value) ? (float) $value : null;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>|null
     */
    public static function optionalArray(array $data, string $key): ?array
    {
        return \is_array($data[$key] ?? null) ? $data[$key] : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function optionalDateTime(array $data, string $key): ?DateTimeImmutable
    {
        $value = $data[$key] ?? null;
        if (!\is_string($value) || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }
}
