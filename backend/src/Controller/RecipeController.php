<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\User;
use App\Service\NutritionalCalculationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/recipes')]
class RecipeController extends AbstractController
{
    #[Route('', name: 'api_recipes_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $recipes = $em->getRepository(Recipe::class)->findBy(['owner' => $this->getUser()]);

        $result = array_map(fn(Recipe $r) => [
            'id' => $r->getId(),
            'name' => $r->getName(),
            'portions' => $r->getPortions(),
            'costPerPortion' => $r->getCostPerPortion(),
            'ingredientCount' => $r->getIngredients()->count(),
            'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $recipes);

        return $this->json($result);
    }

    #[Route('', name: 'api_recipes_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        NutritionalCalculationService $calcService,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $recipe = new Recipe();
        $recipe->setName($data['name'] ?? '');
        $recipe->setPreparationMethod($data['preparationMethod'] ?? null);
        $recipe->setPortions($data['portions'] ?? 1);
        $recipe->setOwner($this->getUser());

        if (!empty($data['ingredients'])) {
            foreach ($data['ingredients'] as $ingredientData) {
                $food = $em->getRepository(Food::class)->find($ingredientData['foodId']);
                if (!$food) continue;

                $ingredient = new RecipeIngredient();
                $ingredient->setFood($food);
                $ingredient->setGrossWeight($ingredientData['grossWeight'] ?? 0);
                $ingredient->setNetWeight($ingredientData['netWeight'] ?? $ingredientData['grossWeight'] ?? 0);
                $ingredient->setCostPerKg($ingredientData['costPerKg'] ?? null);
                $recipe->addIngredient($ingredient);
            }
        }

        $cost = $calcService->calculateRecipeCost($recipe);
        $recipe->setCostPerPortion($cost['cost_per_portion']);

        $em->persist($recipe);
        $em->flush();

        return $this->json($this->serializeRecipe($recipe, $calcService), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_recipes_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em, NutritionalCalculationService $calcService): JsonResponse
    {
        $recipe = $em->getRepository(Recipe::class)->find($id);
        if (!$recipe || $recipe->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Receita nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeRecipe($recipe, $calcService));
    }

    #[Route('/{id}', name: 'api_recipes_update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        NutritionalCalculationService $calcService,
    ): JsonResponse {
        $recipe = $em->getRepository(Recipe::class)->find($id);
        if (!$recipe || $recipe->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Receita nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $recipe->setName($data['name']);
        if (isset($data['preparationMethod'])) $recipe->setPreparationMethod($data['preparationMethod']);
        if (isset($data['portions'])) $recipe->setPortions($data['portions']);

        if (isset($data['ingredients'])) {
            foreach ($recipe->getIngredients()->toArray() as $existing) {
                $recipe->removeIngredient($existing);
                $em->remove($existing);
            }

            foreach ($data['ingredients'] as $ingredientData) {
                $food = $em->getRepository(Food::class)->find($ingredientData['foodId']);
                if (!$food) continue;

                $ingredient = new RecipeIngredient();
                $ingredient->setFood($food);
                $ingredient->setGrossWeight($ingredientData['grossWeight'] ?? 0);
                $ingredient->setNetWeight($ingredientData['netWeight'] ?? $ingredientData['grossWeight'] ?? 0);
                $ingredient->setCostPerKg($ingredientData['costPerKg'] ?? null);
                $recipe->addIngredient($ingredient);
            }

            $cost = $calcService->calculateRecipeCost($recipe);
            $recipe->setCostPerPortion($cost['cost_per_portion']);
        }

        $em->flush();

        return $this->json($this->serializeRecipe($recipe, $calcService));
    }

    #[Route('/{id}', name: 'api_recipes_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $recipe = $em->getRepository(Recipe::class)->find($id);
        if (!$recipe || $recipe->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Receita nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($recipe);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializeRecipe(Recipe $recipe, NutritionalCalculationService $calcService): array
    {
        $nutritionPerPortion = $calcService->calculateRecipeNutritionPerPortion($recipe);
        $nutritionTotal = $calcService->calculateRecipeNutritionTotal($recipe);
        $cost = $calcService->calculateRecipeCost($recipe);

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
