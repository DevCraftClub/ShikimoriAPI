<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\DTO;

use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withId(int|string|null $id)
 * @method self withName(string|null $name)
 * @method self withRussian(string|null $russian)
 * @method self withKind(string|null $kind)
 * @method int|string|null getId()
 * @method string|null getName()
 * @method string|null getRussian()
 * @method string|null getKind()
 */
#[Getter, Setter]
final class GenreDTO extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private int|string|null $id = null;

    #[With]
    private ?string $name = null;

    #[With]
    private ?string $russian = null;

    #[With]
    private ?string $kind = null;

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? null;

        return (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : null)
            ->withName(\is_string($data['name'] ?? null) ? (string) $data['name'] : null)
            ->withRussian(\is_string($data['russian'] ?? null) ? (string) $data['russian'] : null)
            ->withKind(\is_string($data['kind'] ?? null) ? (string) $data['kind'] : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'russian' => $this->russian,
            'kind' => $this->kind,
        ];
    }
}
