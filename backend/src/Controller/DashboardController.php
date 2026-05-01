<?php

namespace App\Controller;

use App\Service\Handler\Dashboard\GetDashboardHandler;
use App\Service\Query\Dashboard\GetDashboardQuery;
use App\Service\Handler\Dashboard\GetMenuAnalysisHandler;
use App\Service\Query\Dashboard\GetMenuAnalysisQuery;
use App\Service\Handler\Dashboard\GetReferencesHandler;
use App\Service\Query\Dashboard\GetReferencesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'api_dashboard', methods: ['GET'])]
    public function index(GetDashboardHandler $handler): JsonResponse
    {
        return $this->json($handler(new GetDashboardQuery($this->getUser())));
    }

    #[Route('/menu-analysis/{menuId}', name: 'api_dashboard_menu_analysis', methods: ['GET'])]
    public function menuAnalysis(int $menuId, GetMenuAnalysisHandler $handler): JsonResponse
    {
        $result = $handler(new GetMenuAnalysisQuery($menuId, $this->getUser()));
        if (!$result) {
            return $this->json(['error' => 'Cardapio nao encontrado.'], 404);
        }

        return $this->json($result);
    }

    #[Route('/references', name: 'api_dashboard_references', methods: ['GET'])]
    public function references(Request $request, GetReferencesHandler $handler): JsonResponse
    {
        return $this->json($handler(new GetReferencesQuery(
            ageGroup: $request->query->get('ageGroup', 'fundamental_6_10'),
            mealPeriod: $request->query->get('mealPeriod', 'parcial_20'),
        )));
    }
}
