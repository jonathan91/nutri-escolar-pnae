<?php

namespace App\Controller;

use App\CQRS\Command\Menu\CreateMenuCommand;
use App\CQRS\Command\Menu\CreateMenuHandler;
use App\CQRS\Command\Menu\DeleteMenuCommand;
use App\CQRS\Command\Menu\DeleteMenuHandler;
use App\CQRS\Query\Menu\GetMenuHandler;
use App\CQRS\Query\Menu\GetMenuQuery;
use App\CQRS\Query\Menu\ListMenusHandler;
use App\CQRS\Query\Menu\ListMenusQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/menus')]
class MenuController extends AbstractController
{
    #[Route('', name: 'api_menus_list', methods: ['GET'])]
    public function list(ListMenusHandler $handler): JsonResponse
    {
        return $this->json($handler(new ListMenusQuery($this->getUser())));
    }

    #[Route('', name: 'api_menus_create', methods: ['POST'])]
    public function create(Request $request, CreateMenuHandler $handler, GetMenuHandler $getHandler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $menu = $handler(new CreateMenuCommand(
                name: $data['name'] ?? '',
                menuDate: $data['menuDate'] ?? 'now',
                mealType: $data['mealType'] ?? 'almoco',
                schoolId: $data['schoolId'] ?? 0,
                studentGroupId: $data['studentGroupId'] ?? 0,
                owner: $this->getUser(),
                items: $data['items'] ?? [],
            ));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        $result = $getHandler(new GetMenuQuery($menu->getId(), $this->getUser()));

        return $this->json($result, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_menus_show', methods: ['GET'])]
    public function show(int $id, GetMenuHandler $handler): JsonResponse
    {
        $result = $handler(new GetMenuQuery($id, $this->getUser()));
        if (!$result) {
            return $this->json(['error' => 'Cardapio nao encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($result);
    }

    #[Route('/{id}', name: 'api_menus_delete', methods: ['DELETE'])]
    public function delete(int $id, DeleteMenuHandler $handler): JsonResponse
    {
        try {
            $handler(new DeleteMenuCommand($id, $this->getUser()));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
