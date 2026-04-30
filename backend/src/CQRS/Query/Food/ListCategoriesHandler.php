<?php

declare(strict_types=1);

namespace App\CQRS\Query\Food;

use App\CQRS\Query\QueryHandlerInterface;
use App\Repository\FoodRepository;

final class ListCategoriesHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FoodRepository $foodRepo,
    ) {}

    public function __invoke(ListCategoriesQuery $query): array
    {
        return $this->foodRepo->findCategories();
    }
}
