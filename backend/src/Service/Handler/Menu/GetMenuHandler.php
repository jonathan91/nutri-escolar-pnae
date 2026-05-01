<?php

declare(strict_types=1);

namespace App\Service\Handler\Menu;

use App\Service\Query\Menu\GetMenuQuery;
use App\Service\Handler\QueryHandlerInterface;
use App\Entity\Menu;
use App\Provider\NutritionalCalculationService;
use App\Provider\PnaeComplianceService;
use Doctrine\ORM\EntityManagerInterface;

final class GetMenuHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NutritionalCalculationService $calcService,
        private readonly PnaeComplianceService $complianceService,
    ) {}

    public function __invoke(GetMenuQuery $query): ?array
    {
        $menu = $this->em->getRepository(Menu::class)->find($query->menuId);
        if (!$menu || $menu->getOwner() !== $query->owner) {
            return null;
        }

        return $this->serialize($menu);
    }

    public function serialize(Menu $menu): array
    {
        $nutrition = $this->calcService->calculateMenuNutrition($menu);
        $ageGroup = $menu->getStudentGroup()->getAgeGroup();
        $mealPeriod = $menu->getStudentGroup()->getMealPeriod();
        $comparison = $this->calcService->compareWithReference($nutrition, $ageGroup, $mealPeriod);
        $alerts = $this->complianceService->checkMenuCompliance($menu);

        $items = [];
        foreach ($menu->getItems() as $item) {
            $itemData = [
                'id' => $item->getId(),
                'portionSize' => $item->getPortionSize(),
                'servings' => $item->getServings(),
                'food' => null,
                'recipe' => null,
            ];

            if ($item->getFood() !== null) {
                $itemData['food'] = [
                    'id' => $item->getFood()->getId(),
                    'name' => $item->getFood()->getName(),
                ];
            }
            if ($item->getRecipe() !== null) {
                $itemData['recipe'] = [
                    'id' => $item->getRecipe()->getId(),
                    'name' => $item->getRecipe()->getName(),
                ];
            }

            $items[] = $itemData;
        }

        return [
            'id' => $menu->getId(),
            'name' => $menu->getName(),
            'menuDate' => $menu->getMenuDate()?->format('Y-m-d'),
            'mealType' => $menu->getMealType(),
            'school' => [
                'id' => $menu->getSchool()->getId(),
                'name' => $menu->getSchool()->getName(),
            ],
            'studentGroup' => [
                'id' => $menu->getStudentGroup()->getId(),
                'name' => $menu->getStudentGroup()->getName(),
                'ageGroup' => $ageGroup,
                'mealPeriod' => $mealPeriod,
            ],
            'items' => $items,
            'nutrition' => $comparison,
            'alerts' => $alerts,
        ];
    }
}
