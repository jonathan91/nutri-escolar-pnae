<?php

declare(strict_types=1);

namespace App\Service\Handler\School;

use App\Service\Command\School\DeleteSchoolCommand;
use App\Service\Handler\CommandHandlerInterface;
use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteSchoolHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(DeleteSchoolCommand $command): void
    {
        $school = $this->em->getRepository(School::class)->find($command->schoolId);
        if (!$school || $school->getOwner() !== $command->owner) {
            throw new \DomainException('Escola nao encontrada.');
        }

        $this->em->remove($school);
        $this->em->flush();
    }
}
