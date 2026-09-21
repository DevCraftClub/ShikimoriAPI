<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Persistence;

use Spiral\Tokenizer\ClassesInterface;

final class EntityClassLocator implements ClassesInterface
{
    /**
     * @param list<class-string> $classes
     */
    public function __construct(
        private readonly array $classes,
    ) {
    }

    /**
     * @return array<class-string, \ReflectionClass<object>>
     */
    public function getClasses(object|string|null $target = null): array
    {
        $result = [];
        foreach ($this->classes as $class) {
            if ($target !== null) {
                $targetName = \is_string($target) ? $target : $target::class;
                if (!\is_a($class, $targetName, true)) {
                    continue;
                }
            }

            $result[$class] = new \ReflectionClass($class);
        }

        return $result;
    }
}
