<?php

namespace App\Controller;

use App\Service\Handler\Food\GetFoodHandler;
use App\Service\Query\Food\GetFoodQuery;
use App\Service\Handler\Food\ListCategoriesHandler;
use App\Service\Query\Food\ListCategoriesQuery;
use App\Service\Handler\Food\ListFoodsHandler;
use App\Service\Query\Food\ListFoodsQuery;
use App\Service\Handler\Food\ListSeasonalFoodsHandler;
use App\Service\Query\Food\ListSeasonalFoodsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/foods')]
class FoodController extends AbstractController
{
    #[Route('', name: 'api_foods_list', methods: ['GET'])]
    public function list(Request $request, ListFoodsHandler $handler): JsonResponse
    {
        return $this->json($handler(new ListFoodsQuery(
            search: $request->query->get('q', ''),
            category: $request->query->get('category'),
            month: $request->query->getInt('month') ?: null,
        )));
    }

    #[Route('/categories', name: 'api_foods_categories', methods: ['GET'])]
    public function categories(ListCategoriesHandler $handler): JsonResponse
    {
        return $this->json($handler(new ListCategoriesQuery()));
    }

    #[Route('/seasonal', name: 'api_foods_seasonal', methods: ['GET'])]
    public function seasonal(Request $request, ListSeasonalFoodsHandler $handler): JsonResponse
    {
        return $this->json($handler(new ListSeasonalFoodsQuery(
            month: $request->query->getInt('month', (int) date('n')),
        )));
    }

    #[Route('/{id}', name: 'api_foods_show', methods: ['GET'])]
    public function show(int $id, GetFoodHandler $handler): JsonResponse
    {
        $result = $handler(new GetFoodQuery($id));
        if (!$result) {
            return $this->json(['error' => 'Alimento nao encontrado.'], 404);
        }

        return $this->json($result);
    }
}
