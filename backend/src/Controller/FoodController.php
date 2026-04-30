<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/foods')]
class FoodController extends AbstractController
{
    #[Route('', name: 'api_foods_list', methods: ['GET'])]
    public function list(Request $request, FoodRepository $foodRepo): JsonResponse
    {
        $query = $request->query->get('q', '');
        $category = $request->query->get('category');
        $month = $request->query->getInt('month');

        if (strlen($query) >= 2) {
            $foods = $foodRepo->search($query, $category, $month ?: null);
        } elseif ($category) {
            $foods = $foodRepo->findByCategory($category);
        } else {
            $foods = $foodRepo->findBy([], ['name' => 'ASC'], 100);
        }

        $result = array_map(fn(Food $f) => $this->serializeFood($f), $foods);

        return $this->json($result);
    }

    #[Route('/categories', name: 'api_foods_categories', methods: ['GET'])]
    public function categories(FoodRepository $foodRepo): JsonResponse
    {
        return $this->json($foodRepo->findCategories());
    }

    #[Route('/seasonal', name: 'api_foods_seasonal', methods: ['GET'])]
    public function seasonal(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $month = $request->query->getInt('month', (int) date('n'));
        $foods = $em->getRepository(Food::class)->findAll();

        $seasonal = array_filter($foods, function (Food $f) use ($month) {
            $months = $f->getSeasonMonths();
            return $months && in_array($month, $months);
        });

        return $this->json(array_map(fn(Food $f) => $this->serializeFood($f), array_values($seasonal)));
    }

    #[Route('/{id}', name: 'api_foods_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $food = $em->getRepository(Food::class)->find($id);
        if (!$food) {
            return $this->json(['error' => 'Alimento nao encontrado.'], 404);
        }

        return $this->json($this->serializeFood($food));
    }

    private function serializeFood(Food $f): array
    {
        return [
            'id' => $f->getId(),
            'tacoId' => $f->getTacoId(),
            'name' => $f->getName(),
            'category' => $f->getCategory(),
            'source' => $f->getSource(),
            'energy' => $f->getEnergy(),
            'protein' => $f->getProtein(),
            'carbohydrate' => $f->getCarbohydrate(),
            'lipid' => $f->getLipid(),
            'fiber' => $f->getFiber(),
            'calcium' => $f->getCalcium(),
            'iron' => $f->getIron(),
            'magnesium' => $f->getMagnesium(),
            'zinc' => $f->getZinc(),
            'vitaminA' => $f->getVitaminA(),
            'vitaminC' => $f->getVitaminC(),
            'sodium' => $f->getSodium(),
            'saturatedFat' => $f->getSaturatedFat(),
            'ultraProcessed' => $f->isUltraProcessed(),
            'containsGluten' => $f->isContainsGluten(),
            'containsLactose' => $f->isContainsLactose(),
            'allergens' => $f->getAllergens(),
            'seasonMonths' => $f->getSeasonMonths(),
        ];
    }
}
