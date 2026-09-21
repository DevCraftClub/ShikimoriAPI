<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Filter;

use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use Devcraft\Attributes\WithItem;
use DevCraftClub\Shikimori\Enum\AnimeOrder;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withPage(int $page)
 * @method self withLimit(int $limit)
 * @method self withSearch(string|null $search)
 * @method self withOrder(AnimeOrder|null $order)
 * @method self withKind(string|null $kind)
 * @method self withStatus(string|null $status)
 * @method self withSeason(string|null $season)
 * @method self withScore(int|null $score)
 * @method self withIdsItem(int|string $id)
 * @method int getPage()
 * @method int getLimit()
 * @method string|null getSearch()
 * @method AnimeOrder|null getOrder()
 * @method string|null getKind()
 * @method string|null getStatus()
 * @method string|null getSeason()
 * @method int|null getScore()
 * @method list<int|string> getIds()
 */
#[Getter, Setter]
final class AnimeListFilter extends AbstractWith
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
    private ?string $search = null;

    #[With]
    private ?AnimeOrder $order = null;

    #[With]
    private ?string $kind = null;

    #[With]
    private ?string $status = null;

    #[With]
    private ?string $season = null;

    #[With]
    private ?int $score = null;

    /** @var list<int|string> */
    #[With, WithItem(['int', 'string'])]
    private array $ids = [];

    /**
     * @return array<string, mixed>
     */
    public function toGraphQLVariables(): array
    {
        $variables = [
            'page' => $this->page,
            'limit' => min($this->limit, 50),
            'search' => $this->search,
            'order' => $this->order?->value,
            'kind' => $this->kind,
            'status' => $this->status,
            'season' => $this->season,
            'score' => $this->score,
            'ids' => $this->ids === [] ? null : implode(',', $this->ids),
        ];

        return array_filter(
            $variables,
            static fn (mixed $value): bool => $value !== null && $value !== ''
        );
    }
}
