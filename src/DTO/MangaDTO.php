<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\DTO;

use DateTimeImmutable;
use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use Devcraft\Attributes\WithItem;
use DevCraftClub\Shikimori\Util\ArrayUtil;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withId(int|string $id)
 * @method self withName(string $name)
 * @method self withRussian(string|null $russian)
 * @method self withKind(string|null $kind)
 * @method self withStatus(string|null $status)
 * @method self withScore(float|null $score)
 * @method self withVolumes(int|null $volumes)
 * @method self withChapters(int|null $chapters)
 * @method self withDescription(string|null $description)
 * @method self withUrl(string|null $url)
 * @method self withAiredOn(string|null $airedOn)
 * @method self withReleasedOn(string|null $releasedOn)
 * @method self withUpdatedAt(DateTimeImmutable|null $updatedAt)
 * @method self withPoster(PosterDTO|null $poster)
 * @method self withGenresItem(GenreDTO $genre)
 * @method int|string getId()
 * @method string getName()
 * @method string|null getRussian()
 * @method string|null getKind()
 * @method string|null getStatus()
 * @method float|null getScore()
 * @method int|null getVolumes()
 * @method int|null getChapters()
 * @method string|null getDescription()
 * @method string|null getUrl()
 * @method string|null getAiredOn()
 * @method string|null getReleasedOn()
 * @method DateTimeImmutable|null getUpdatedAt()
 * @method PosterDTO|null getPoster()
 * @method list<GenreDTO> getGenres()
 */
#[Getter, Setter]
final class MangaDTO extends AbstractWith
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
    private ?string $kind = null;

    #[With]
    private ?string $status = null;

    #[With]
    private ?float $score = null;

    #[With]
    private ?int $volumes = null;

    #[With]
    private ?int $chapters = null;

    #[With]
    private ?string $description = null;

    #[With]
    private ?string $url = null;

    #[With]
    private ?string $airedOn = null;

    #[With]
    private ?string $releasedOn = null;

    #[With]
    private ?DateTimeImmutable $updatedAt = null;

    #[With]
    private ?PosterDTO $poster = null;

    /** @var list<GenreDTO> */
    #[With, WithItem(GenreDTO::class)]
    private array $genres = [];

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $manga = (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : 0)
            ->withName(ArrayUtil::stringOrDefault($data, 'name', ''))
            ->withRussian(ArrayUtil::optionalString($data, 'russian'))
            ->withKind(ArrayUtil::optionalString($data, 'kind'))
            ->withStatus(ArrayUtil::optionalString($data, 'status'))
            ->withScore(ArrayUtil::optionalFloat($data, 'score'))
            ->withVolumes(ArrayUtil::optionalInt($data, 'volumes'))
            ->withChapters(ArrayUtil::optionalInt($data, 'chapters'))
            ->withDescription(ArrayUtil::optionalString($data, 'description'))
            ->withUrl(ArrayUtil::optionalString($data, 'url'))
            ->withAiredOn(ArrayUtil::optionalString($data, 'airedOn'))
            ->withReleasedOn(ArrayUtil::optionalString($data, 'releasedOn'))
            ->withUpdatedAt(ArrayUtil::optionalDateTime($data, 'updatedAt'))
            ->withPoster(PosterDTO::fromArray(ArrayUtil::optionalArray($data, 'poster')));

        $genres = ArrayUtil::optionalArray($data, 'genres');
        if ($genres !== null) {
            foreach ($genres as $genre) {
                if (\is_array($genre)) {
                    $manga = $manga->withGenresItem(GenreDTO::fromArray($genre));
                }
            }
        }

        return $manga;
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
            'status' => $this->status,
            'score' => $this->score,
            'volumes' => $this->volumes,
            'chapters' => $this->chapters,
            'description' => $this->description,
            'url' => $this->url,
            'airedOn' => $this->airedOn,
            'releasedOn' => $this->releasedOn,
            'updatedAt' => $this->updatedAt?->format(DateTimeImmutable::ATOM),
            'poster' => $this->poster?->toArray(),
            'genres' => array_map(
                static fn (GenreDTO $genre): array => $genre->toArray(),
                $this->genres
            ),
        ];
    }
}
