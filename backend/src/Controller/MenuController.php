<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\Recipe;
use App\Entity\School;
use App\Entity\StudentGroup;
use App\Service\NutritionalCalculationService;
use App\Service\PnaeComplianceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/menus')]
class MenuController extends AbstractController
{
    #[Route('', name: 'api_menus_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $menus = $em->getRepository(Menu::class)->findBy(['owner' => $this->getUser()], ['menuDate' => 'DESC']);

        $result = array_map(fn(Menu $m) => [
            'id' => $m->getId(),
            'name' => $m->getName(),
            'menuDate' => $m->getMenuDate()?->format('Y-m-d'),
            'mealType' => $m->getMealType(),
            'schoolName' => $m->getSchool()->getName(),
            'studentGroupName' => $m->getStudentGroup()->getName(),
            'itemCount' => $m->getItems()->count(),
        ], $menus);

        return $this->json($result);
    }

    #[Route('', name: 'api_menus_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        NutritionalCalculationService $calcService,
        PnaeComplianceService $complianceService,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $school = $em->getRepository(School::class)->find($data['schoolId'] ?? 0);
        $studentGroup = $em->getRepository(StudentGroup::class)->find($data['studentGroupId'] ?? 0);

        if (!$school || $school->getOwner() !== $user) {
            return $this->json(['error' => 'Escola nao encontrada.'], Response::HTTP_NOT_FOUND);
        }
        if (!$studentGroup) {
            return $this->json(['error' => 'Turma nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $menu = new Menu();
        $menu->setName($data['name'] ?? '');
        $menu->setMenuDate(new \DateTime($data['menuDate'] ?? 'now'));
        $menu->setMealType($data['mealType'] ?? 'almoco');
        $menu->setSchool($school);
        $menu->setStudentGroup($studentGroup);
        $menu->setOwner($user);

        if (!empty($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $menuItem = new MenuItem();
                $menuItem->setPortionSize($itemData['portionSize'] ?? 100);
                $menuItem->setServings($itemData['servings'] ?? 1);

                if (!empty($itemData['foodId'])) {
                    $food = $em->getRepository(Food::class)->find($itemData['foodId']);
                    $menuItem->setFood($food);
                }
                if (!empty($itemData['recipeId'])) {
                    $recipe = $em->getRepository(Recipe::class)->find($itemData['recipeId']);
                    $menuItem->setRecipe($recipe);
                }

                $menu->addItem($menuItem);
            }
        }

        $em->persist($menu);
        $em->flush();

        return $this->json($this->serializeMenu($menu, $calcService, $complianceService), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_menus_show', methods: ['GET'])]
    public function show(
        int $id,
        EntityManagerInterface $em,
        NutritionalCalculationService $calcService,
        PnaeComplianceService $complianceService,
    ): JsonResponse {
        $menu = $em->getRepository(Menu::class)->find($id);
        if (!$menu || $menu->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Cardapio nao encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->serializeMenu($menu, $calcService, $complianceService));
    }

    #[Route('/{id}', name: 'api_menus_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $menu = $em->getRepository(Menu::class)->find($id);
        if (!$menu || $menu->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Cardapio nao encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($menu);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializeMenu(
        Menu $menu,
        NutritionalCalculationService $calcService,
        PnaeComplianceService $complianceService,
    ): array {
        $nutrition = $calcService->calculateMenuNutrition($menu);
        $ageGroup = $menu->getStudentGroup()->getAgeGroup();
        $mealPeriod = $menu->getStudentGroup()->getMealPeriod();
        $comparison = $calcService->compareWithReference($nutrition, $ageGroup, $mealPeriod);
        $alerts = $complianceService->checkMenuCompliance($menu);

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
