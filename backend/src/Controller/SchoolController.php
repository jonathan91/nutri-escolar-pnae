<?php

namespace App\Controller;

use App\Service\Command\School\CreateSchoolCommand;
use App\Service\Handler\School\CreateSchoolHandler;
use App\Service\Command\School\CreateStudentGroupCommand;
use App\Service\Handler\School\CreateStudentGroupHandler;
use App\Service\Command\School\DeleteSchoolCommand;
use App\Service\Handler\School\DeleteSchoolHandler;
use App\Service\Command\School\DeleteStudentGroupCommand;
use App\Service\Handler\School\DeleteStudentGroupHandler;
use App\Service\Command\School\UpdateSchoolCommand;
use App\Service\Handler\School\UpdateSchoolHandler;
use App\Service\Handler\School\GetSchoolHandler;
use App\Service\Query\School\GetSchoolQuery;
use App\Service\Handler\School\ListSchoolsHandler;
use App\Service\Query\School\ListSchoolsQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/schools')]
class SchoolController extends AbstractController
{
    #[Route('', name: 'api_schools_list', methods: ['GET'])]
    public function list(ListSchoolsHandler $handler): JsonResponse
    {
        return $this->json($handler(new ListSchoolsQuery($this->getUser())));
    }

    #[Route('', name: 'api_schools_create', methods: ['POST'])]
    public function create(Request $request, CreateSchoolHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $school = $handler(new CreateSchoolCommand(
            name: $data['name'] ?? '',
            owner: $this->getUser(),
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            inepCode: $data['inepCode'] ?? null,
        ));

        return $this->json([
            'id' => $school->getId(),
            'name' => $school->getName(),
            'city' => $school->getCity(),
            'state' => $school->getState(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_schools_show', methods: ['GET'])]
    public function show(int $id, GetSchoolHandler $handler): JsonResponse
    {
        $result = $handler(new GetSchoolQuery($id, $this->getUser()));
        if (!$result) {
            return $this->json(['error' => 'Escola nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($result);
    }

    #[Route('/{id}', name: 'api_schools_update', methods: ['PUT'])]
    public function update(int $id, Request $request, UpdateSchoolHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $handler(new UpdateSchoolCommand(
                schoolId: $id,
                owner: $this->getUser(),
                name: $data['name'] ?? null,
                city: $data['city'] ?? null,
                state: $data['state'] ?? null,
                inepCode: $data['inepCode'] ?? null,
            ));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Escola atualizada.']);
    }

    #[Route('/{id}', name: 'api_schools_delete', methods: ['DELETE'])]
    public function delete(int $id, DeleteSchoolHandler $handler): JsonResponse
    {
        try {
            $handler(new DeleteSchoolCommand($id, $this->getUser()));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{schoolId}/student-groups', name: 'api_student_groups_create', methods: ['POST'])]
    public function createStudentGroup(int $schoolId, Request $request, CreateStudentGroupHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        try {
            $group = $handler(new CreateStudentGroupCommand(
                schoolId: $schoolId,
                name: $data['name'] ?? '',
                ageGroup: $data['ageGroup'] ?? '',
                mealPeriod: $data['mealPeriod'] ?? '',
                studentCount: $data['studentCount'] ?? 0,
                owner: $this->getUser(),
            ));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $group->getId(),
            'name' => $group->getName(),
            'ageGroup' => $group->getAgeGroup(),
            'mealPeriod' => $group->getMealPeriod(),
            'studentCount' => $group->getStudentCount(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{schoolId}/student-groups/{groupId}', name: 'api_student_groups_delete', methods: ['DELETE'])]
    public function deleteStudentGroup(int $schoolId, int $groupId, DeleteStudentGroupHandler $handler): JsonResponse
    {
        try {
            $handler(new DeleteStudentGroupCommand($schoolId, $groupId, $this->getUser()));
        } catch (\DomainException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
