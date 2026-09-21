<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Filter;

use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use DevCraftClub\Shikimori\Enum\UserRateStatus;
use DevCraftClub\Shikimori\Enum\UserRateTargetType;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withPage(int $page)
 * @method self withLimit(int $limit)
 * @method self withUserId(int|string|null $userId)
 * @method self withTargetType(UserRateTargetType|null $targetType)
 * @method self withStatus(UserRateStatus|null $status)
 * @method int getPage()
 * @method int getLimit()
 * @method int|string|null getUserId()
 * @method UserRateTargetType|null getTargetType()
 * @method UserRateStatus|null getStatus()
 */
#[Getter, Setter]
final class UserRateListFilter extends AbstractWith
{
    public function __construct()
    {
        parent::__construct();
    }

    #[With]
    private int $page = 1;

    #[With]
    private int $limit = 20;

    #[With]
    private int|string|null $userId = null;

    #[With]
    private ?UserRateTargetType $targetType = null;

    #[With]
    private ?UserRateStatus $status = null;

    /**
     * @return array<string, mixed>
     */
    public function toGraphQLVariables(): array
    {
        $variables = [
            'page' => $this->page,
            'limit' => min($this->limit, 50),
            'userId' => $this->userId,
            'targetType' => $this->targetType?->value,
            'status' => $this->status?->value,
        ];

        return array_filter(
            $variables,
            static fn (mixed $value): bool => $value !== null && $value !== ''
        );
    }
}
