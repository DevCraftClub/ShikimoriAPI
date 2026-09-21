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
 * @method self withEpisodes(int|null $episodes)
 * @method self withEpisodesAired(int|null $episodesAired)
 * @method self withDescription(string|null $description)
 * @method self withUrl(string|null $url)
 * @method self withDuration(int|null $duration)
 * @method self withRating(string|null $rating)
 * @method self withFranchise(string|null $franchise)
 * @method self withAiredOn(string|null $airedOn)
 * @method self withReleasedOn(string|null $releasedOn)
 * @method self withUpdatedAt(DateTimeImmutable|null $updatedAt)
 * @method self withPoster(PosterDTO|null $poster)
 * @method self withGenreItem(GenreDTO $genre)
 * @method self withStudioItem(StudioDTO $studio)
 * @method int|string getId()
 * @method string getName()
 * @method string|null getRussian()
 * @method string|null getKind()
 * @method string|null getStatus()
 * @method float|null getScore()
 * @method int|null getEpisodes()
 * @method int|null getEpisodesAired()
 * @method string|null getDescription()
 * @method string|null getUrl()
 * @method int|null getDuration()
 * @method string|null getRating()
 * @method string|null getFranchise()
 * @method string|null getAiredOn()
 * @method string|null getReleasedOn()
 * @method DateTimeImmutable|null getUpdatedAt()
 * @method PosterDTO|null getPoster()
 * @method list<GenreDTO> getGenres()
 * @method list<StudioDTO> getStudios()
 */
#[Getter, Setter]
final class AnimeDTO extends AbstractWith
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
    private ?int $episodes = null;

    #[With]
    private ?int $episodesAired = null;

    #[With]
    private ?string $description = null;

    #[With]
    private ?string $url = null;

    #[With]
    private ?int $duration = null;

    #[With]
    private ?string $rating = null;

    #[With]
    private ?string $franchise = null;

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

    /** @var list<StudioDTO> */
    #[With, WithItem(StudioDTO::class)]
    private array $studios = [];

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;
        $anime = (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : 0)
            ->withName(ArrayUtil::stringOrDefault($data, 'name', ''))
            ->withRussian(ArrayUtil::optionalString($data, 'russian'))
            ->withKind(ArrayUtil::optionalString($data, 'kind'))
            ->withStatus(ArrayUtil::optionalString($data, 'status'))
            ->withScore(ArrayUtil::optionalFloat($data, 'score'))
            ->withEpisodes(ArrayUtil::optionalInt($data, 'episodes'))
            ->withEpisodesAired(ArrayUtil::optionalInt($data, 'episodesAired'))
            ->withDescription(ArrayUtil::optionalString($data, 'description'))
            ->withUrl(ArrayUtil::optionalString($data, 'url'))
            ->withDuration(ArrayUtil::optionalInt($data, 'duration'))
            ->withRating(ArrayUtil::optionalString($data, 'rating'))
            ->withFranchise(ArrayUtil::optionalString($data, 'franchise'))
            ->withAiredOn(ArrayUtil::optionalString($data, 'airedOn'))
            ->withReleasedOn(ArrayUtil::optionalString($data, 'releasedOn'))
            ->withPoster(PosterDTO::fromArray(ArrayUtil::optionalArray($data, 'poster')))
            ->withUpdatedAt(ArrayUtil::optionalDateTime($data, 'updatedAt'));

        $genres = ArrayUtil::optionalListOfMaps($data, 'genres');
        if ($genres !== null) {
            foreach ($genres as $genre) {
                $anime = $anime->withGenresItem(GenreDTO::fromArray($genre));
            }
        }

        $studios = ArrayUtil::optionalListOfMaps($data, 'studios');
        if ($studios !== null) {
            foreach ($studios as $studio) {
                $studioDto = StudioDTO::fromArray($studio);
                if ($studioDto !== null) {
                    $anime = $anime->withStudiosItem($studioDto);
                }
            }
        }

        return $anime;
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
            'episodes' => $this->episodes,
            'episodesAired' => $this->episodesAired,
            'description' => $this->description,
            'url' => $this->url,
            'duration' => $this->duration,
            'rating' => $this->rating,
            'franchise' => $this->franchise,
            'airedOn' => $this->airedOn,
            'releasedOn' => $this->releasedOn,
            'updatedAt' => $this->updatedAt?->format(DateTimeImmutable::ATOM),
            'poster' => $this->poster?->toArray(),
            'genres' => array_map(
                static fn (GenreDTO $genre): array => $genre->toArray(),
                $this->genres
            ),
            'studios' => array_map(
                static fn (StudioDTO $studio): array => $studio->toArray(),
                $this->studios
            ),
        ];
    }
}
