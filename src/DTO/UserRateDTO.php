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
 * @method self withScore(int|null $score)
 * @method self withStatus(string|null $status)
 * @method self withText(string|null $text)
 * @method self withEpisodes(int|null $episodes)
 * @method self withChapters(int|null $chapters)
 * @method self withRewatches(int|null $rewatches)
 * @method self withCreatedAt(DateTimeImmutable|null $createdAt)
 * @method self withUpdatedAt(DateTimeImmutable|null $updatedAt)
 * @method self withUser(UserDTO|null $user)
 * @method self withTarget(AnimeDTO|null $target)
 * @method int|string getId()
 * @method int|null getScore()
 * @method string|null getStatus()
 * @method string|null getText()
 * @method int|null getEpisodes()
 * @method int|null getChapters()
 * @method int|null getRewatches()
 * @method DateTimeImmutable|null getCreatedAt()
 * @method DateTimeImmutable|null getUpdatedAt()
 * @method UserDTO|null getUser()
 * @method AnimeDTO|null getTarget()
 */
#[Getter, Setter]
final class UserRateDTO extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private int|string $id = 0;

    #[With]
    private ?int $score = null;

    #[With]
    private ?string $status = null;

    #[With]
    private ?string $text = null;

    #[With]
    private ?int $episodes = null;

    #[With]
    private ?int $chapters = null;

    #[With]
    private ?int $rewatches = null;

    #[With]
    private ?DateTimeImmutable $createdAt = null;

    #[With]
    private ?DateTimeImmutable $updatedAt = null;

    #[With]
    private ?UserDTO $user = null;

    #[With]
    private ?AnimeDTO $target = null;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $target = $data['target'] ?? null;

        return (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : 0)
            ->withScore(ArrayUtil::optionalInt($data, 'score'))
            ->withStatus(ArrayUtil::optionalString($data, 'status'))
            ->withText(ArrayUtil::optionalString($data, 'text'))
            ->withEpisodes(ArrayUtil::optionalInt($data, 'episodes'))
            ->withChapters(ArrayUtil::optionalInt($data, 'chapters'))
            ->withRewatches(ArrayUtil::optionalInt($data, 'rewatches'))
            ->withCreatedAt(ArrayUtil::optionalDateTime($data, 'createdAt'))
            ->withUpdatedAt(ArrayUtil::optionalDateTime($data, 'updatedAt'))
            ->withUser(\is_array($data['user'] ?? null) ? UserDTO::fromArray($data['user']) : null)
            ->withTarget(\is_array($target) ? AnimeDTO::fromArray($target) : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'score' => $this->score,
            'status' => $this->status,
            'text' => $this->text,
            'episodes' => $this->episodes,
            'chapters' => $this->chapters,
            'rewatches' => $this->rewatches,
            'createdAt' => $this->createdAt?->format(DateTimeImmutable::ATOM),
            'updatedAt' => $this->updatedAt?->format(DateTimeImmutable::ATOM),
            'user' => $this->user?->toArray(),
            'target' => $this->target?->toArray(),
        ];
    }
}
