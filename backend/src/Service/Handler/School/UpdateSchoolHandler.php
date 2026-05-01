<?php

declare(strict_types=1);

namespace App\Service\Handler\School;

use App\Service\Command\School\UpdateSchoolCommand;
use App\Service\Handler\CommandHandlerInterface;
use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;

final class UpdateSchoolHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(UpdateSchoolCommand $command): School
    {
        $school = $this->em->getRepository(School::class)->find($command->schoolId);
        if (!$school || $school->getOwner() !== $command->owner) {
            throw new \DomainException('Escola nao encontrada.');
        }

        if ($command->name !== null) $school->setName($command->name);
        if ($command->city !== null) $school->setCity($command->city);
        if ($command->state !== null) $school->setState($command->state);
        if ($command->inepCode !== null) $school->setInepCode($command->inepCode);

        $this->em->flush();

        return $school;
    }
}
