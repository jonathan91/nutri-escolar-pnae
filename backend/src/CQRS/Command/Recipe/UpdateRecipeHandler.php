<?php

declare(strict_types=1);

namespace App\CQRS\Command\Recipe;

use App\CQRS\Command\CommandHandlerInterface;
use App\Entity\Food;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Service\NutritionalCalculationService;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateRecipeHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NutritionalCalculationService $calcService,
    ) {}

    public function __invoke(UpdateRecipeCommand $command): Recipe
    {
        $recipe = $this->em->getRepository(Recipe::class)->find($command->recipeId);
        if (!$recipe || $recipe->getOwner() !== $command->owner) {
            throw new \DomainException('Receita nao encontrada.');
        }

        if ($command->name !== null) $recipe->setName($command->name);
        if ($command->preparationMethod !== null) $recipe->setPreparationMethod($command->preparationMethod);
        if ($command->portions !== null) $recipe->setPortions($command->portions);

        if ($command->ingredients !== null) {
            foreach ($recipe->getIngredients()->toArray() as $existing) {
                $recipe->removeIngredient($existing);
                $this->em->remove($existing);
            }

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
        }

        $this->em->flush();

        return $recipe;
    }
}
