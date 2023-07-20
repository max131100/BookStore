<?php

namespace App\Model;

use App\Service\Recommendation\Model\RecommendationItem;

class RecommendedBookListResponse
{
    /**
     * @var RecommendedBook[]
     */
    private array $items;

    /**
     * @param RecommendedBook[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return RecommendedBook[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
