<?php

declare(strict_types=1);

namespace DevCraftClub\Shikimori\Filter;

use Devcraft\Abstracts\AbstractWith;
use Devcraft\Attributes\With;
use Devcraft\Attributes\WithItem;
use Lombok\Getter;
use Lombok\Setter;

/**
 * @method self withPage(int $page)
 * @method self withLimit(int $limit)
 * @method self withSearch(string|null $search)
 * @method self withIdsItem(int|string $id)
 * @method int getPage()
 * @method int getLimit()
 * @method string|null getSearch()
 * @method list<int|string> getIds()
 */
#[Getter, Setter]
final class MangaListFilter extends AbstractWith
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
            'ids' => $this->ids === [] ? null : implode(',', $this->ids),
        ];

        return array_filter(
            $variables,
            static fn (mixed $value): bool => $value !== null && $value !== ''
        );
    }
}
