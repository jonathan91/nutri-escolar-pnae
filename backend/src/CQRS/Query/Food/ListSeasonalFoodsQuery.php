<?php

declare(strict_types=1);

namespace App\CQRS\Query\Food;

use App\CQRS\Query\QueryInterface;

final readonly class ListSeasonalFoodsQuery implements QueryInterface
{
    public function __construct(
        public int $month,
    ) {}
}
