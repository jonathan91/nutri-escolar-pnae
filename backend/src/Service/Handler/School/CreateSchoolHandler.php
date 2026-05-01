<?php

declare(strict_types=1);

namespace App\Service\Handler\School;

use App\Service\Command\School\CreateSchoolCommand;
use App\Service\Handler\CommandHandlerInterface;
use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;

final class CreateSchoolHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(CreateSchoolCommand $command): School
    {
        $school = new School();
        $school->setName($command->name);
        $school->setCity($command->city);
        $school->setState($command->state);
        $school->setInepCode($command->inepCode);
        $school->setOwner($command->owner);

        $this->em->persist($school);
        $this->em->flush();

        return $school;
    }
}
