<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\DTO;

use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withId(string|null $id)
 * @method self withOriginalUrl(string|null $originalUrl)
 * @method self withMainUrl(string|null $mainUrl)
 * @method string|null getId()
 * @method string|null getOriginalUrl()
 * @method string|null getMainUrl()
 */
#[Getter, Setter]
final class PosterDTO extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private ?string $id = null;

    #[With]
    private ?string $originalUrl = null;

    #[With]
    private ?string $mainUrl = null;

    /**
     * @param array<string, mixed>|null $data
     */
    public static function fromArray(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        return (new self())
            ->withId(\is_string($data['id'] ?? null) ? (string) $data['id'] : null)
            ->withOriginalUrl(\is_string($data['originalUrl'] ?? null) ? (string) $data['originalUrl'] : null)
            ->withMainUrl(\is_string($data['mainUrl'] ?? null) ? (string) $data['mainUrl'] : null);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'originalUrl' => $this->originalUrl,
            'mainUrl' => $this->mainUrl,
        ];
    }
}
