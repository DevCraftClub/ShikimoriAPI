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
 * @method self withTitle(string $title)
 * @method self withDescription(string|null $description)
 * @method self withUrl(string|null $url)
 * @method self withStartedOn(string|null $startedOn)
 * @method self withFinishedOn(string|null $finishedOn)
 * @method self withCreatedAt(DateTimeImmutable|null $createdAt)
 * @method self withUpdatedAt(DateTimeImmutable|null $updatedAt)
 * @method int|string getId()
 * @method string getTitle()
 * @method string|null getDescription()
 * @method string|null getUrl()
 * @method string|null getStartedOn()
 * @method string|null getFinishedOn()
 * @method DateTimeImmutable|null getCreatedAt()
 * @method DateTimeImmutable|null getUpdatedAt()
 */
#[Getter, Setter]
final class ContestDTO extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private int|string $id = 0;

    #[With]
    private string $title = '';

    #[With]
    private ?string $description = null;

    #[With]
    private ?string $url = null;

    #[With]
    private ?string $startedOn = null;

    #[With]
    private ?string $finishedOn = null;

    #[With]
    private ?DateTimeImmutable $createdAt = null;

    #[With]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;

        return (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : 0)
            ->withTitle(ArrayUtil::stringOrDefault($data, 'title', ''))
            ->withDescription(ArrayUtil::optionalString($data, 'description'))
            ->withUrl(ArrayUtil::optionalString($data, 'url'))
            ->withStartedOn(ArrayUtil::optionalString($data, 'startedOn'))
            ->withFinishedOn(ArrayUtil::optionalString($data, 'finishedOn'))
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
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'startedOn' => $this->startedOn,
            'finishedOn' => $this->finishedOn,
            'createdAt' => $this->createdAt?->format(DateTimeImmutable::ATOM),
            'updatedAt' => $this->updatedAt?->format(DateTimeImmutable::ATOM),
        ];
    }
}
