<?php

namespace App\Controller;

use App\CQRS\Command\Recipe\CreateRecipeCommand;
use App\CQRS\Command\Recipe\CreateRecipeHandler;
use App\CQRS\Command\Recipe\DeleteRecipeCommand;
use App\CQRS\Command\Recipe\DeleteRecipeHandler;
use App\CQRS\Command\Recipe\UpdateRecipeCommand;
use App\CQRS\Command\Recipe\UpdateRecipeHandler;
use App\CQRS\Query\Recipe\GetRecipeHandler;
use App\CQRS\Query\Recipe\GetRecipeQuery;
use App\CQRS\Query\Recipe\ListRecipesHandler;
use App\CQRS\Query\Recipe\ListRecipesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/recipes')]
class RecipeController extends AbstractController
{
    #[Route('', name: 'api_recipes_list', methods: ['GET'])]
    public function list(ListRecipesHandler $handler): JsonResponse
    {
        return $this->json($handler(new ListRecipesQuery($this->getUser())));
    }

    #[Route('', name: 'api_recipes_create', methods: ['POST'])]
    public function create(Request $request, CreateRecipeHandler $handler, GetRecipeHandler $getHandler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $recipe = $handler(new CreateRecipeCommand(
            name: $data['name'] ?? '',
            owner: $this->getUser(),
            preparationMethod: $data['preparationMethod'] ?? null,
            portions: $data['portions'] ?? 1,
            ingredients: $data['ingredients'] ?? [],
        ));

        $result = $getHandler(new GetRecipeQuery($recipe->getId(), $this->getUser()));

        return $this->json($result, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_recipes_show', methods: ['GET'])]
    public function show(int $id, GetRecipeHandler $handler): JsonResponse
    {
        $result = $handler(new GetRecipeQuery($id, $this->getUser()));
        if (!$result) {
            return $this->json(['error' => 'Receita nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($result);
    }

    #[Route('/{id}', name: 'api_recipes_update', methods: ['PUT'])]
    public function update(int $id, Request $request, UpdateRecipeHandler $handler, GetRecipeHandler $getHandler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $handler(new UpdateRecipeCommand(
                recipeId: $id,
                owner: $this->getUser(),
                name: $data['name'] ?? null,
                preparationMethod: $data['preparationMethod'] ?? null,
                portions: $data['portions'] ?? null,
                ingredients: $data['ingredients'] ?? null,
            ));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $result = $getHandler(new GetRecipeQuery($id, $this->getUser()));

        return $this->json($result);
    }

    #[Route('/{id}', name: 'api_recipes_delete', methods: ['DELETE'])]
    public function delete(int $id, DeleteRecipeHandler $handler): JsonResponse
    {
        try {
            $handler(new DeleteRecipeCommand($id, $this->getUser()));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
