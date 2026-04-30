<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\School;
use App\Entity\Recipe;
use App\Service\NutritionalCalculationService;
use App\Service\NutritionalReferenceService;
use App\Service\PnaeComplianceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'api_dashboard', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        $schoolCount = $em->getRepository(School::class)->count(['owner' => $user]);
        $menuCount = $em->getRepository(Menu::class)->count(['owner' => $user]);
        $recipeCount = $em->getRepository(Recipe::class)->count(['owner' => $user]);

        return $this->json([
            'schools' => $schoolCount,
            'menus' => $menuCount,
            'recipes' => $recipeCount,
        ]);
    }

    #[Route('/menu-analysis/{menuId}', name: 'api_dashboard_menu_analysis', methods: ['GET'])]
    public function menuAnalysis(
        int $menuId,
        EntityManagerInterface $em,
        NutritionalCalculationService $calcService,
        NutritionalReferenceService $refService,
        PnaeComplianceService $complianceService,
    ): JsonResponse {
        $menu = $em->getRepository(Menu::class)->find($menuId);
        if (!$menu || $menu->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Cardapio nao encontrado.'], 404);
        }

        $nutrition = $calcService->calculateMenuNutrition($menu);
        $ageGroup = $menu->getStudentGroup()->getAgeGroup();
        $mealPeriod = $menu->getStudentGroup()->getMealPeriod();
        $comparison = $calcService->compareWithReference($nutrition, $ageGroup, $mealPeriod);
        $alerts = $complianceService->checkMenuCompliance($menu);

        return $this->json([
            'menu' => [
                'id' => $menu->getId(),
                'name' => $menu->getName(),
                'menuDate' => $menu->getMenuDate()?->format('Y-m-d'),
            ],
            'analysis' => $comparison,
            'alerts' => $alerts,
        ]);
    }

    #[Route('/references', name: 'api_dashboard_references', methods: ['GET'])]
    public function references(
        Request $request,
        NutritionalReferenceService $refService,
    ): JsonResponse {
        $ageGroup = $request->query->get('ageGroup', 'fundamental_6_10');
        $mealPeriod = $request->query->get('mealPeriod', 'parcial_20');

        return $this->json([
            'daily' => $refService->getDailyReference($ageGroup),
            'meal' => $refService->getMealReference($ageGroup, $mealPeriod),
            'ageGroups' => $refService->getAllAgeGroups(),
        ]);
    }
}
