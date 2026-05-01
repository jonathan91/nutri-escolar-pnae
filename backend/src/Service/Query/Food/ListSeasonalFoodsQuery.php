<?php

declare(strict_types=1);

namespace App\Service\Query\Food;

use App\Service\Query\QueryInterface;

final readonly class ListSeasonalFoodsQuery implements QueryInterface
{
    public function __construct(
        public int $month,
    ) {}
}
