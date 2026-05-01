<?php

declare(strict_types=1);

namespace App\Service\Query\Food;

use App\Service\Query\QueryInterface;

final readonly class ListFoodsQuery implements QueryInterface
{
    public function __construct(
        public string $search = '',
        public ?string $category = null,
        public ?int $month = null,
    ) {}
}
