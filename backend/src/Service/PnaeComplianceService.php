<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\MenuItem;

class PnaeComplianceService
{
    public function __construct(
        private readonly NutritionalReferenceService $referenceService,
        private readonly NutritionalCalculationService $calculationService,
    ) {}

    public function checkMenuCompliance(Menu $menu): array
    {
        $alerts = [];
        $ageGroup = $menu->getStudentGroup()->getAgeGroup();
        $mealPeriod = $menu->getStudentGroup()->getMealPeriod();
        $reference = $this->referenceService->getMealReference($ageGroup, $mealPeriod);
        $nutrition = $this->calculationService->calculateMenuNutrition($menu);

        $alerts = array_merge($alerts, $this->checkAddedSugar($menu, $ageGroup, $reference));
        $alerts = array_merge($alerts, $this->checkSodium($nutrition, $reference));
        $alerts = array_merge($alerts, $this->checkUltraProcessed($menu));
        $alerts = array_merge($alerts, $this->checkSaturatedFat($nutrition, $reference));
        $alerts = array_merge($alerts, $this->checkAllergens($menu));

        return $alerts;
    }

    private function checkAddedSugar(Menu $menu, string $ageGroup, array $reference): array
    {
        $alerts = [];

        if (!$reference['added_sugar_allowed']) {
            foreach ($menu->getItems() as $item) {
                $hasAddedSugar = false;
                $itemName = '';

                if ($item->getFood() !== null && ($item->getFood()->getAddedSugar() ?? 0) > 0) {
                    $hasAddedSugar = true;
                    $itemName = $item->getFood()->getName();
                }

                if ($item->getRecipe() !== null) {
                    foreach ($item->getRecipe()->getIngredients() as $ingredient) {
                        if (($ingredient->getFood()->getAddedSugar() ?? 0) > 0) {
                            $hasAddedSugar = true;
                            $itemName = $item->getRecipe()->getName();
                            break;
                        }
                    }
                }

                if ($hasAddedSugar) {
                    $label = $this->referenceService->getAgeGroupLabel($ageGroup);
                    $alerts[] = [
                        'type' => 'error',
                        'code' => 'ADDED_SUGAR_PROHIBITED',
                        'message' => "Acucar adicionado e proibido para {$label}. Item: {$itemName}",
                    ];
                }
            }
        }

        return $alerts;
    }

    private function checkSodium(array $nutrition, array $reference): array
    {
        $alerts = [];

        if ($nutrition['sodium'] > $reference['sodium_max']) {
            $alerts[] = [
                'type' => 'warning',
                'code' => 'SODIUM_EXCEEDED',
                'message' => sprintf(
                    'Sodio (%.1f mg) ultrapassa o limite de %.1f mg para esta refeicao.',
                    $nutrition['sodium'],
                    $reference['sodium_max']
                ),
            ];
        }

        return $alerts;
    }

    private function checkUltraProcessed(Menu $menu): array
    {
        $alerts = [];
        $ultraProcessedCount = 0;
        $totalCount = 0;

        foreach ($menu->getItems() as $item) {
            $totalCount++;
            if ($item->getFood() !== null && $item->getFood()->isUltraProcessed()) {
                $ultraProcessedCount++;
            }
        }

        if ($totalCount > 0 && ($ultraProcessedCount / $totalCount) > 0.20) {
            $alerts[] = [
                'type' => 'warning',
                'code' => 'ULTRA_PROCESSED_EXCEEDED',
                'message' => sprintf(
                    'Alimentos ultraprocessados representam %d%% do cardapio (maximo recomendado: 20%%).',
                    round(($ultraProcessedCount / $totalCount) * 100)
                ),
            ];
        }

        return $alerts;
    }

    private function checkSaturatedFat(array $nutrition, array $reference): array
    {
        $alerts = [];

        if ($nutrition['energy'] > 0) {
            $saturatedFatPct = ($nutrition['saturated_fat'] * 9 / $nutrition['energy']) * 100;
            if ($saturatedFatPct > $reference['saturated_fat_pct_max']) {
                $alerts[] = [
                    'type' => 'warning',
                    'code' => 'SATURATED_FAT_EXCEEDED',
                    'message' => sprintf(
                        'Gordura saturada (%.1f%% do VET) ultrapassa o limite de %d%% do VET.',
                        $saturatedFatPct,
                        $reference['saturated_fat_pct_max']
                    ),
                ];
            }
        }

        return $alerts;
    }

    private function checkAllergens(Menu $menu): array
    {
        $alerts = [];
        $allergenSet = [];

        foreach ($menu->getItems() as $item) {
            if ($item->getFood() !== null) {
                $this->collectAllergens($item->getFood(), $allergenSet);
            }
            if ($item->getRecipe() !== null) {
                foreach ($item->getRecipe()->getIngredients() as $ingredient) {
                    $this->collectAllergens($ingredient->getFood(), $allergenSet);
                }
            }
        }

        if (!empty($allergenSet)) {
            $alerts[] = [
                'type' => 'info',
                'code' => 'ALLERGENS_PRESENT',
                'message' => 'Alergenos presentes: ' . implode(', ', array_unique($allergenSet)),
            ];
        }

        return $alerts;
    }

    private function collectAllergens($food, array &$allergenSet): void
    {
        if ($food->isContainsGluten()) {
            $allergenSet[] = 'Gluten';
        }
        if ($food->isContainsLactose()) {
            $allergenSet[] = 'Lactose';
        }
        if ($food->getAllergens()) {
            foreach ($food->getAllergens() as $allergen) {
                $allergenSet[] = $allergen;
            }
        }
    }
}
