<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Util;

use DateTimeImmutable;

final class ArrayUtil
{
    /**
     * @param array<array-key, mixed> $data
     * @return array<string, mixed>
     */
    public static function stringKeyed(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function stringOrDefault(array $data, string $key, string $default): string
    {
        $value = $data[$key] ?? null;

        return \is_string($value) ? $value : $default;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function optionalString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return \is_string($value) ? $value : null;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function optionalInt(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;

        return \is_int($value) ? $value : null;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function optionalFloat(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;

        return \is_int($value) || \is_float($value) ? (float) $value : null;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return array<string, mixed>|null
     */
    public static function optionalArray(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;

        return \is_array($value) ? self::stringKeyed($value) : null;
    }

    /**
     * @param array<array-key, mixed> $data
     * @return list<array<string, mixed>>|null
     */
    public static function optionalListOfMaps(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if (!\is_array($value)) {
            return null;
        }

        $result = [];
        foreach ($value as $item) {
            if (\is_array($item)) {
                $result[] = self::stringKeyed($item);
            }
        }

        return $result;
    }

    /**
     * @param array<array-key, mixed> $data
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
