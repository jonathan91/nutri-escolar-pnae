<?php

namespace App\Service;

use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;

class NutritionalCalculationService
{
    public function __construct(
        private readonly NutritionalReferenceService $referenceService,
    ) {}

    public function calculateMenuNutrition(Menu $menu): array
    {
        $totals = $this->emptyNutritionArray();

        foreach ($menu->getItems() as $item) {
            $itemNutrition = $this->calculateMenuItemNutrition($item);
            foreach ($totals as $key => $val) {
                $totals[$key] += $itemNutrition[$key];
            }
        }

        return array_map(fn($v) => round($v, 2), $totals);
    }

    public function calculateMenuItemNutrition(MenuItem $item): array
    {
        if ($item->getRecipe() !== null) {
            $recipeNutrition = $this->calculateRecipeNutritionPerPortion($item->getRecipe());
            $factor = ($item->getPortionSize() / 100) * $item->getServings();
            return array_map(fn($v) => round($v * $factor, 2), $recipeNutrition);
        }

        if ($item->getFood() !== null) {
            return $this->calculateFoodNutrition($item->getFood(), $item->getPortionSize() * $item->getServings());
        }

        return $this->emptyNutritionArray();
    }

    public function calculateRecipeNutritionPerPortion(Recipe $recipe): array
    {
        $totals = $this->emptyNutritionArray();

        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredientNutrition = $this->calculateIngredientNutrition($ingredient);
            foreach ($totals as $key => $val) {
                $totals[$key] += $ingredientNutrition[$key];
            }
        }

        $portions = max(1, $recipe->getPortions());
        return array_map(fn($v) => round($v / $portions, 2), $totals);
    }

    public function calculateRecipeNutritionTotal(Recipe $recipe): array
    {
        $totals = $this->emptyNutritionArray();

        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredientNutrition = $this->calculateIngredientNutrition($ingredient);
            foreach ($totals as $key => $val) {
                $totals[$key] += $ingredientNutrition[$key];
            }
        }

        return array_map(fn($v) => round($v, 2), $totals);
    }

    public function calculateIngredientNutrition(RecipeIngredient $ingredient): array
    {
        $food = $ingredient->getFood();
        $weightG = $ingredient->getNetWeight();
        return $this->calculateFoodNutrition($food, $weightG);
    }

    private function calculateFoodNutrition($food, float $weightG): array
    {
        $factor = $weightG / 100;
        return [
            'energy'        => round(($food->getEnergy() ?? 0) * $factor, 2),
            'protein'       => round(($food->getProtein() ?? 0) * $factor, 2),
            'carbohydrate'  => round(($food->getCarbohydrate() ?? 0) * $factor, 2),
            'lipid'         => round(($food->getLipid() ?? 0) * $factor, 2),
            'fiber'         => round(($food->getFiber() ?? 0) * $factor, 2),
            'calcium'       => round(($food->getCalcium() ?? 0) * $factor, 2),
            'iron'          => round(($food->getIron() ?? 0) * $factor, 2),
            'magnesium'     => round(($food->getMagnesium() ?? 0) * $factor, 2),
            'zinc'          => round(($food->getZinc() ?? 0) * $factor, 2),
            'vitamin_a'     => round(($food->getVitaminA() ?? 0) * $factor, 2),
            'vitamin_c'     => round(($food->getVitaminC() ?? 0) * $factor, 2),
            'sodium'        => round(($food->getSodium() ?? 0) * $factor, 2),
            'saturated_fat' => round(($food->getSaturatedFat() ?? 0) * $factor, 2),
            'added_sugar'   => round(($food->getAddedSugar() ?? 0) * $factor, 2),
        ];
    }

    public function compareWithReference(array $nutrition, string $ageGroup, string $mealPeriod): array
    {
        $reference = $this->referenceService->getMealReference($ageGroup, $mealPeriod);

        return [
            'nutrition' => $nutrition,
            'reference' => $reference,
            'adequacy' => [
                'energy' => $reference['energy'] > 0 ? round(($nutrition['energy'] / $reference['energy']) * 100, 1) : 0,
                'protein' => $reference['protein_min'] > 0 ? round(($nutrition['protein'] / $reference['protein_min']) * 100, 1) : 0,
                'fiber' => $reference['fiber'] > 0 ? round(($nutrition['fiber'] / $reference['fiber']) * 100, 1) : 0,
                'calcium' => $reference['calcium'] > 0 ? round(($nutrition['calcium'] / $reference['calcium']) * 100, 1) : 0,
                'iron' => $reference['iron'] > 0 ? round(($nutrition['iron'] / $reference['iron']) * 100, 1) : 0,
                'magnesium' => $reference['magnesium'] > 0 ? round(($nutrition['magnesium'] / $reference['magnesium']) * 100, 1) : 0,
                'zinc' => $reference['zinc'] > 0 ? round(($nutrition['zinc'] / $reference['zinc']) * 100, 1) : 0,
                'vitamin_a' => $reference['vitamin_a'] > 0 ? round(($nutrition['vitamin_a'] / $reference['vitamin_a']) * 100, 1) : 0,
                'vitamin_c' => $reference['vitamin_c'] > 0 ? round(($nutrition['vitamin_c'] / $reference['vitamin_c']) * 100, 1) : 0,
            ],
        ];
    }

    public function calculateRecipeCost(Recipe $recipe): array
    {
        $totalCost = 0;
        foreach ($recipe->getIngredients() as $ingredient) {
            $costPerKg = $ingredient->getCostPerKg() ?? 0;
            $totalCost += ($ingredient->getGrossWeight() / 1000) * $costPerKg;
        }

        return [
            'total_cost' => round($totalCost, 2),
            'cost_per_portion' => round($totalCost / max(1, $recipe->getPortions()), 2),
        ];
    }

    private function emptyNutritionArray(): array
    {
        return [
            'energy' => 0, 'protein' => 0, 'carbohydrate' => 0, 'lipid' => 0,
            'fiber' => 0, 'calcium' => 0, 'iron' => 0, 'magnesium' => 0,
            'zinc' => 0, 'vitamin_a' => 0, 'vitamin_c' => 0, 'sodium' => 0,
            'saturated_fat' => 0, 'added_sugar' => 0,
        ];
    }
}
