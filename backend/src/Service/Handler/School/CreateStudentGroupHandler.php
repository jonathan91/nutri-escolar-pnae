<?php

declare(strict_types=1);

namespace App\Service\Handler\School;

use App\Service\Handler\CommandHandlerInterface;
use App\Entity\School;
use App\Entity\StudentGroup;
use Doctrine\ORM\EntityManagerInterface;

final class CreateStudentGroupHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(CreateStudentGroupCommand $command): StudentGroup
    {
        $school = $this->em->getRepository(School::class)->find($command->schoolId);
        if (!$school || $school->getOwner() !== $command->owner) {
            throw new \DomainException('Escola nao encontrada.');
        }

        $group = new StudentGroup();
        $group->setName($command->name);
        $group->setAgeGroup($command->ageGroup);
        $group->setMealPeriod($command->mealPeriod);
        $group->setStudentCount($command->studentCount);
        $group->setSchool($school);

        $this->em->persist($group);
        $this->em->flush();

        return $group;
    }
}
