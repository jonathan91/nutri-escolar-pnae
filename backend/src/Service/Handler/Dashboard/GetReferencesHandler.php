<?php

declare(strict_types=1);

namespace App\Service\Handler\Dashboard;

use App\Service\Query\Dashboard\GetReferencesQuery;
use App\Service\Handler\QueryHandlerInterface;
use App\Provider\NutritionalReferenceService;

final class GetReferencesHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly NutritionalReferenceService $refService,
    ) {}

    public function __invoke(GetReferencesQuery $query): array
    {
        return [
            'daily' => $this->refService->getDailyReference($query->ageGroup),
            'meal' => $this->refService->getMealReference($query->ageGroup, $query->mealPeriod),
            'ageGroups' => $this->refService->getAllAgeGroups(),
        ];
    }
}
