<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\StudentGroup;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/schools')]
class SchoolController extends AbstractController
{
    #[Route('', name: 'api_schools_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $schools = $em->getRepository(School::class)->findBy(['owner' => $user]);

        $result = array_map(fn(School $s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'city' => $s->getCity(),
            'state' => $s->getState(),
            'inepCode' => $s->getInepCode(),
            'studentGroupCount' => $s->getStudentGroups()->count(),
        ], $schools);

        return $this->json($result);
    }

    #[Route('', name: 'api_schools_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $school = new School();
        $school->setName($data['name'] ?? '');
        $school->setCity($data['city'] ?? null);
        $school->setState($data['state'] ?? null);
        $school->setInepCode($data['inepCode'] ?? null);
        $school->setOwner($user);

        $em->persist($school);
        $em->flush();

        return $this->json([
            'id' => $school->getId(),
            'name' => $school->getName(),
            'city' => $school->getCity(),
            'state' => $school->getState(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_schools_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $school = $em->getRepository(School::class)->find($id);
        if (!$school || $school->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Escola nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $groups = array_map(fn(StudentGroup $sg) => [
            'id' => $sg->getId(),
            'name' => $sg->getName(),
            'ageGroup' => $sg->getAgeGroup(),
            'mealPeriod' => $sg->getMealPeriod(),
            'studentCount' => $sg->getStudentCount(),
        ], $school->getStudentGroups()->toArray());

        return $this->json([
            'id' => $school->getId(),
            'name' => $school->getName(),
            'city' => $school->getCity(),
            'state' => $school->getState(),
            'inepCode' => $school->getInepCode(),
            'studentGroups' => $groups,
        ]);
    }

    #[Route('/{id}', name: 'api_schools_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $school = $em->getRepository(School::class)->find($id);
        if (!$school || $school->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Escola nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) $school->setName($data['name']);
        if (isset($data['city'])) $school->setCity($data['city']);
        if (isset($data['state'])) $school->setState($data['state']);
        if (isset($data['inepCode'])) $school->setInepCode($data['inepCode']);

        $em->flush();

        return $this->json(['message' => 'Escola atualizada.']);
    }

    #[Route('/{id}', name: 'api_schools_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $school = $em->getRepository(School::class)->find($id);
        if (!$school || $school->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Escola nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($school);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{schoolId}/student-groups', name: 'api_student_groups_create', methods: ['POST'])]
    public function createStudentGroup(int $schoolId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $school = $em->getRepository(School::class)->find($schoolId);
        if (!$school || $school->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Escola nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $group = new StudentGroup();
        $group->setName($data['name'] ?? '');
        $group->setAgeGroup($data['ageGroup'] ?? '');
        $group->setMealPeriod($data['mealPeriod'] ?? '');
        $group->setStudentCount($data['studentCount'] ?? 0);
        $group->setSchool($school);

        $em->persist($group);
        $em->flush();

        return $this->json([
            'id' => $group->getId(),
            'name' => $group->getName(),
            'ageGroup' => $group->getAgeGroup(),
            'mealPeriod' => $group->getMealPeriod(),
            'studentCount' => $group->getStudentCount(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{schoolId}/student-groups/{groupId}', name: 'api_student_groups_delete', methods: ['DELETE'])]
    public function deleteStudentGroup(int $schoolId, int $groupId, EntityManagerInterface $em): JsonResponse
    {
        $group = $em->getRepository(StudentGroup::class)->find($groupId);
        if (!$group || $group->getSchool()->getId() !== $schoolId || $group->getSchool()->getOwner() !== $this->getUser()) {
            return $this->json(['error' => 'Turma nao encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($group);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
