<?php

declare(strict_types=1);

namespace App\CQRS\Query\Dashboard;

use App\CQRS\Query\QueryHandlerInterface;
use App\Service\NutritionalReferenceService;

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
