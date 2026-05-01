<?php

declare(strict_types=1);

namespace App\Service\Handler\Dashboard;

use App\Service\Handler\QueryHandlerInterface;
use App\Entity\Menu;
use App\Provider\NutritionalCalculationService;
use App\Provider\PnaeComplianceService;
use Doctrine\ORM\EntityManagerInterface;

final class GetMenuAnalysisHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NutritionalCalculationService $calcService,
        private readonly PnaeComplianceService $complianceService,
    ) {}

    public function __invoke(GetMenuAnalysisQuery $query): ?array
    {
        $menu = $this->em->getRepository(Menu::class)->find($query->menuId);
        if (!$menu || $menu->getOwner() !== $query->owner) {
            return null;
        }

        $nutrition = $this->calcService->calculateMenuNutrition($menu);
        $ageGroup = $menu->getStudentGroup()->getAgeGroup();
        $mealPeriod = $menu->getStudentGroup()->getMealPeriod();
        $comparison = $this->calcService->compareWithReference($nutrition, $ageGroup, $mealPeriod);
        $alerts = $this->complianceService->checkMenuCompliance($menu);

        return [
            'menu' => [
                'id' => $menu->getId(),
                'name' => $menu->getName(),
                'menuDate' => $menu->getMenuDate()?->format('Y-m-d'),
            ],
            'analysis' => $comparison,
            'alerts' => $alerts,
        ];
    }
}
