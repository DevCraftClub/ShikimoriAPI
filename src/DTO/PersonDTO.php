<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\DTO;

use DateTimeImmutable;
use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use DevCraftClub\Shikimori\Util\ArrayUtil;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withId(int|string $id)
 * @method self withName(string $name)
 * @method self withRussian(string|null $russian)
 * @method self withJapanese(string|null $japanese)
 * @method self withDescription(string|null $description)
 * @method self withUrl(string|null $url)
 * @method self withPoster(PosterDTO|null $poster)
 * @method self withCreatedAt(DateTimeImmutable|null $createdAt)
 * @method self withUpdatedAt(DateTimeImmutable|null $updatedAt)
 * @method int|string getId()
 * @method string getName()
 * @method string|null getRussian()
 * @method string|null getJapanese()
 * @method string|null getDescription()
 * @method string|null getUrl()
 * @method PosterDTO|null getPoster()
 * @method DateTimeImmutable|null getCreatedAt()
 * @method DateTimeImmutable|null getUpdatedAt()
 */
#[Getter, Setter]
final class PersonDTO extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private int|string $id = 0;

    #[With]
    private string $name = '';

    #[With]
    private ?string $russian = null;

    #[With]
    private ?string $japanese = null;

    #[With]
    private ?string $description = null;

    #[With]
    private ?string $url = null;

    #[With]
    private ?PosterDTO $poster = null;

    #[With]
    private ?DateTimeImmutable $createdAt = null;

    #[With]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;

        return (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : 0)
            ->withName(ArrayUtil::stringOrDefault($data, 'name', ''))
            ->withRussian(ArrayUtil::optionalString($data, 'russian'))
            ->withJapanese(ArrayUtil::optionalString($data, 'japanese'))
            ->withDescription(ArrayUtil::optionalString($data, 'description'))
            ->withUrl(ArrayUtil::optionalString($data, 'url'))
            ->withPoster(PosterDTO::fromArray(ArrayUtil::optionalArray($data, 'poster')))
            ->withCreatedAt(ArrayUtil::optionalDateTime($data, 'createdAt'))
            ->withUpdatedAt(ArrayUtil::optionalDateTime($data, 'updatedAt'));
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
            'japanese' => $this->japanese,
            'description' => $this->description,
            'url' => $this->url,
            'poster' => $this->poster?->toArray(),
            'createdAt' => $this->createdAt?->format(DateTimeImmutable::ATOM),
            'updatedAt' => $this->updatedAt?->format(DateTimeImmutable::ATOM),
        ];
    }
}
