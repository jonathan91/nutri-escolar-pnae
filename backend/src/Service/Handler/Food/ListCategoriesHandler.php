<?php

declare(strict_types=1);

namespace App\Service\Handler\Food;

use App\Service\Query\Food\ListCategoriesQuery;
use App\Service\Handler\QueryHandlerInterface;
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
