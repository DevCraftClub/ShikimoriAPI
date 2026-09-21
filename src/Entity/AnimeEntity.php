<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;
use DateTimeImmutable;
use DevCraftClub\Shikimori\DTO\AnimeDTO;
use DevCraftClub\Shikimori\Persistence\StorableEntity;
use DevCraftClub\Shikimori\Util\ArrayUtil;

#[Entity(table: 'shikimori_animes')]
class AnimeEntity implements StorableEntity
{
    #[Column(type: 'integer', primary: true)]
    private int $id = 0;

    #[Column(type: 'string')]
    private string $name = '';

    #[Column(type: 'string', nullable: true)]
    private ?string $russian = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $kind = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $status = null;

    #[Column(type: 'float', nullable: true)]
    private ?float $score = null;

    #[Column(type: 'integer', nullable: true)]
    private ?int $episodes = null;

    #[Column(type: 'integer', nullable: true)]
    private ?int $episodesAired = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $url = null;

    #[Column(type: 'integer', nullable: true)]
    private ?int $duration = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $rating = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $franchise = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $airedOn = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $releasedOn = null;

    #[Column(type: 'datetime', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    /** @var array<string, mixed>|null */
    #[Column(type: 'json', nullable: true)]
    private ?array $poster = null;

    /** @var list<array<string, mixed>>|null */
    #[Column(type: 'json', nullable: true)]
    private ?array $genres = null;

    /** @var list<array<string, mixed>>|null */
    #[Column(type: 'json', nullable: true)]
    private ?array $studios = null;

    #[Column(type: 'datetime')]
    private DateTimeImmutable $fetchedAt;

    public function __construct()
    {
        $this->fetchedAt = new DateTimeImmutable();
    }

    public static function createFromArray(array $data): static
    {
        /** @phpstan-ignore-next-line Cycle may subclass entity */
        $entity = new static();
        $entity->updateFromArray($data);

        return $entity;
    }

    public function updateFromArray(array $data): void
    {
        $this->setId(self::optionalInt($data['id'] ?? null) ?? 0);
        $this->setName(\is_string($data['name'] ?? null) ? (string) $data['name'] : '');
        $this->setRussian(\is_string($data['russian'] ?? null) ? (string) $data['russian'] : null);
        $this->setKind(\is_string($data['kind'] ?? null) ? (string) $data['kind'] : null);
        $this->setStatus(\is_string($data['status'] ?? null) ? (string) $data['status'] : null);
        $this->setScore(self::optionalFloat($data['score'] ?? null));
        $this->setEpisodes(self::optionalInt($data['episodes'] ?? null));
        $this->setEpisodesAired(self::optionalInt($data['episodesAired'] ?? null));
        $this->setDescription(\is_string($data['description'] ?? null) ? (string) $data['description'] : null);
        $this->setUrl(\is_string($data['url'] ?? null) ? (string) $data['url'] : null);
        $this->setDuration(self::optionalInt($data['duration'] ?? null));
        $this->setRating(\is_string($data['rating'] ?? null) ? (string) $data['rating'] : null);
        $this->setFranchise(\is_string($data['franchise'] ?? null) ? (string) $data['franchise'] : null);
        $this->setAiredOn(\is_string($data['airedOn'] ?? null) ? (string) $data['airedOn'] : null);
        $this->setReleasedOn(\is_string($data['releasedOn'] ?? null) ? (string) $data['releasedOn'] : null);
        $this->setUpdatedAt(self::parseDateTime($data['updatedAt'] ?? null));
        $this->setPoster(ArrayUtil::optionalArray($data, 'poster'));
        $this->setGenres(ArrayUtil::optionalListOfMaps($data, 'genres'));
        $this->setStudios(ArrayUtil::optionalListOfMaps($data, 'studios'));
        $this->markFetchedNow();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'russian' => $this->getRussian(),
            'kind' => $this->getKind(),
            'status' => $this->getStatus(),
            'score' => $this->getScore(),
            'episodes' => $this->getEpisodes(),
            'episodesAired' => $this->getEpisodesAired(),
            'description' => $this->getDescription(),
            'url' => $this->getUrl(),
            'duration' => $this->getDuration(),
            'rating' => $this->getRating(),
            'franchise' => $this->getFranchise(),
            'airedOn' => $this->getAiredOn(),
            'releasedOn' => $this->getReleasedOn(),
            'updatedAt' => $this->getUpdatedAt()?->format(DateTimeImmutable::ATOM),
            'poster' => $this->getPoster(),
            'genres' => $this->getGenres(),
            'studios' => $this->getStudios(),
            'fetchedAt' => $this->getFetchedAt()->getTimestamp(),
        ];
    }

    public function toDto(): AnimeDTO
    {
        return AnimeDTO::fromArray($this->toArray());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRussian(): ?string
    {
        return $this->russian;
    }

    public function setRussian(?string $russian): void
    {
        $this->russian = $russian;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(?string $kind): void
    {
        $this->kind = $kind;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): void
    {
        $this->score = $score;
    }

    public function getEpisodes(): ?int
    {
        return $this->episodes;
    }

    public function setEpisodes(?int $episodes): void
    {
        $this->episodes = $episodes;
    }

    public function getEpisodesAired(): ?int
    {
        return $this->episodesAired;
    }

    public function setEpisodesAired(?int $episodesAired): void
    {
        $this->episodesAired = $episodesAired;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    public function getRating(): ?string
    {
        return $this->rating;
    }

    public function setRating(?string $rating): void
    {
        $this->rating = $rating;
    }

    public function getFranchise(): ?string
    {
        return $this->franchise;
    }

    public function setFranchise(?string $franchise): void
    {
        $this->franchise = $franchise;
    }

    public function getAiredOn(): ?string
    {
        return $this->airedOn;
    }

    public function setAiredOn(?string $airedOn): void
    {
        $this->airedOn = $airedOn;
    }

    public function getReleasedOn(): ?string
    {
        return $this->releasedOn;
    }

    public function setReleasedOn(?string $releasedOn): void
    {
        $this->releasedOn = $releasedOn;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPoster(): ?array
    {
        return $this->poster;
    }

    /**
     * @param array<string, mixed>|null $poster
     */
    public function setPoster(?array $poster): void
    {
        $this->poster = $poster;
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    public function getGenres(): ?array
    {
        return $this->genres;
    }

    /**
     * @param list<array<string, mixed>>|null $genres
     */
    public function setGenres(?array $genres): void
    {
        $this->genres = $genres;
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    public function getStudios(): ?array
    {
        return $this->studios;
    }

    /**
     * @param list<array<string, mixed>>|null $studios
     */
    public function setStudios(?array $studios): void
    {
        $this->studios = $studios;
    }

    public function getFetchedAt(): DateTimeImmutable
    {
        return $this->fetchedAt;
    }

    public function setFetchedAt(DateTimeImmutable $fetchedAt): void
    {
        $this->fetchedAt = $fetchedAt;
    }

    public function markFetchedNow(): void
    {
        $this->setFetchedAt(new DateTimeImmutable());
    }

    private static function parseDateTime(mixed $value): ?DateTimeImmutable
    {
        if (!\is_string($value) || $value === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function optionalInt(mixed $value): ?int
    {
        return \is_int($value) ? $value : null;
    }

    private static function optionalFloat(mixed $value): ?float
    {
        return \is_int($value) || \is_float($value) ? (float) $value : null;
    }
}
