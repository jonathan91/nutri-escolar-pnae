<?php

declare(strict_types=1);

namespace App\CQRS\Query\School;

use App\CQRS\Query\QueryHandlerInterface;
use App\Entity\School;
use App\Entity\StudentGroup;
use Doctrine\ORM\EntityManagerInterface;

final class GetSchoolHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(GetSchoolQuery $query): ?array
    {
        $school = $this->em->getRepository(School::class)->find($query->schoolId);
        if (!$school || $school->getOwner() !== $query->owner) {
            return null;
        }

        $groups = array_map(fn(StudentGroup $sg) => [
            'id' => $sg->getId(),
            'name' => $sg->getName(),
            'ageGroup' => $sg->getAgeGroup(),
            'mealPeriod' => $sg->getMealPeriod(),
            'studentCount' => $sg->getStudentCount(),
        ], $school->getStudentGroups()->toArray());

        return [
            'id' => $school->getId(),
            'name' => $school->getName(),
            'city' => $school->getCity(),
            'state' => $school->getState(),
            'inepCode' => $school->getInepCode(),
            'studentGroups' => $groups,
        ];
    }
}
