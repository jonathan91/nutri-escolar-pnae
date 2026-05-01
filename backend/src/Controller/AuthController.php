<?php

namespace App\Controller;

use App\CQRS\Command\Auth\RegisterUserCommand;
use App\CQRS\Command\Auth\RegisterUserHandler;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, RegisterUserHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $user = $handler(new RegisterUserCommand(
                email: $data['email'] ?? '',
                name: $data['name'] ?? '',
                password: $data['password'] ?? '',
                crn: $data['crn'] ?? null,
            ));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return $this->json([
            'message' => 'Usuario cadastrado com sucesso.',
            'user' => ['id' => $user->getId(), 'email' => $user->getEmail(), 'name' => $user->getName()],
        ], Response::HTTP_CREATED);
    }

    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->json(['error' => 'Nao autenticado.'], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'crn' => $user->getCrn(),
            'roles' => $user->getRoles(),
        ]);
    }
}
