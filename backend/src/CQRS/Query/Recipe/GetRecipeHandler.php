<?php

declare(strict_types=1);

namespace App\CQRS\Query\Recipe;

use App\CQRS\Query\QueryHandlerInterface;
use App\Entity\Recipe;
use App\Service\NutritionalCalculationService;
use Doctrine\ORM\EntityManagerInterface;

final class GetRecipeHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NutritionalCalculationService $calcService,
    ) {}

    public function __invoke(GetRecipeQuery $query): ?array
    {
        $recipe = $this->em->getRepository(Recipe::class)->find($query->recipeId);
        if (!$recipe || $recipe->getOwner() !== $query->owner) {
            return null;
        }

        $nutritionPerPortion = $this->calcService->calculateRecipeNutritionPerPortion($recipe);
        $nutritionTotal = $this->calcService->calculateRecipeNutritionTotal($recipe);
        $cost = $this->calcService->calculateRecipeCost($recipe);

        $ingredients = [];
        foreach ($recipe->getIngredients() as $ingredient) {
            $ingredients[] = [
                'id' => $ingredient->getId(),
                'food' => [
                    'id' => $ingredient->getFood()->getId(),
                    'name' => $ingredient->getFood()->getName(),
                ],
                'grossWeight' => $ingredient->getGrossWeight(),
                'netWeight' => $ingredient->getNetWeight(),
                'costPerKg' => $ingredient->getCostPerKg(),
            ];
        }

        return [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'preparationMethod' => $recipe->getPreparationMethod(),
            'portions' => $recipe->getPortions(),
            'ingredients' => $ingredients,
            'nutritionPerPortion' => $nutritionPerPortion,
            'nutritionTotal' => $nutritionTotal,
            'cost' => $cost,
            'createdAt' => $recipe->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
