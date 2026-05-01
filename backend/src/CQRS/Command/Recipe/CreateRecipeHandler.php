<?php

declare(strict_types=1);

namespace App\CQRS\Command\Recipe;

use App\CQRS\Command\CommandHandlerInterface;
use App\Entity\Food;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Service\NutritionalCalculationService;
use Doctrine\ORM\EntityManagerInterface;

final class CreateRecipeHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NutritionalCalculationService $calcService,
    ) {}

    public function __invoke(CreateRecipeCommand $command): Recipe
    {
        $recipe = new Recipe();
        $recipe->setName($command->name);
        $recipe->setPreparationMethod($command->preparationMethod);
        $recipe->setPortions($command->portions);
        $recipe->setOwner($command->owner);

        foreach ($command->ingredients as $ingredientData) {
            $food = $this->em->getRepository(Food::class)->find($ingredientData['foodId'] ?? 0);
            if (!$food) continue;

            $ingredient = new RecipeIngredient();
            $ingredient->setFood($food);
            $ingredient->setGrossWeight($ingredientData['grossWeight'] ?? 0);
            $ingredient->setNetWeight($ingredientData['netWeight'] ?? $ingredientData['grossWeight'] ?? 0);
            $ingredient->setCostPerKg($ingredientData['costPerKg'] ?? null);
            $recipe->addIngredient($ingredient);
        }

        $cost = $this->calcService->calculateRecipeCost($recipe);
        $recipe->setCostPerPortion($cost['cost_per_portion']);

        $this->em->persist($recipe);
        $this->em->flush();

        return $recipe;
    }
}
