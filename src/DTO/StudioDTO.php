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
 * @method self withImageUrl(string|null $imageUrl)
 * @method int|string|null getId()
 * @method string|null getName()
 * @method string|null getImageUrl()
 */
#[Getter, Setter]
final class StudioDTO extends AbstractWith
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
    private ?string $imageUrl = null;

    /**
     * @param array<string, mixed>|null $data
     */
    public static function fromArray(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        $id = $data['id'] ?? null;

        return (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : null)
            ->withName(\is_string($data['name'] ?? null) ? (string) $data['name'] : null)
            ->withImageUrl(\is_string($data['imageUrl'] ?? null) ? (string) $data['imageUrl'] : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'imageUrl' => $this->imageUrl,
        ];
    }
}
