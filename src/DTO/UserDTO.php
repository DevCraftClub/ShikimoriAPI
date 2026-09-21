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
 * @method self withNickname(string $nickname)
 * @method self withAvatar(string|null $avatar)
 * @method self withLastOnlineAt(DateTimeImmutable|null $lastOnlineAt)
 * @method self withUrl(string|null $url)
 * @method self withName(string|null $name)
 * @method self withSex(string|null $sex)
 * @method self withFullYears(int|null $fullYears)
 * @method self withCreatedAt(DateTimeImmutable|null $createdAt)
 * @method int|string getId()
 * @method string getNickname()
 * @method string|null getAvatar()
 * @method DateTimeImmutable|null getLastOnlineAt()
 * @method string|null getUrl()
 * @method string|null getName()
 * @method string|null getSex()
 * @method int|null getFullYears()
 * @method DateTimeImmutable|null getCreatedAt()
 */
#[Getter, Setter]
final class UserDTO extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private int|string $id = 0;

    #[With]
    private string $nickname = '';

    #[With]
    private ?string $avatar = null;

    #[With]
    private ?DateTimeImmutable $lastOnlineAt = null;

    #[With]
    private ?string $url = null;

    #[With]
    private ?string $name = null;

    #[With]
    private ?string $sex = null;

    #[With]
    private ?int $fullYears = null;

    #[With]
    private ?DateTimeImmutable $createdAt = null;

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $id = $data['id'] ?? 0;

        return (new self())
            ->withId(\is_int($id) || \is_string($id) ? $id : 0)
            ->withNickname(ArrayUtil::stringOrDefault($data, 'nickname', ''))
            ->withAvatar(ArrayUtil::optionalString($data, 'avatar'))
            ->withLastOnlineAt(ArrayUtil::optionalDateTime($data, 'lastOnlineAt'))
            ->withUrl(ArrayUtil::optionalString($data, 'url'))
            ->withName(ArrayUtil::optionalString($data, 'name'))
            ->withSex(ArrayUtil::optionalString($data, 'sex'))
            ->withFullYears(ArrayUtil::optionalInt($data, 'fullYears'))
            ->withCreatedAt(ArrayUtil::optionalDateTime($data, 'createdAt'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'avatar' => $this->avatar,
            'lastOnlineAt' => $this->lastOnlineAt?->format(DateTimeImmutable::ATOM),
            'url' => $this->url,
            'name' => $this->name,
            'sex' => $this->sex,
            'fullYears' => $this->fullYears,
            'createdAt' => $this->createdAt?->format(DateTimeImmutable::ATOM),
        ];
    }
}
