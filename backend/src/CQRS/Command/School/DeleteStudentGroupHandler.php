<?php

declare(strict_types=1);

namespace App\CQRS\Command\School;

use App\CQRS\Command\CommandHandlerInterface;
use App\Entity\StudentGroup;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteStudentGroupHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(DeleteStudentGroupCommand $command): void
    {
        $group = $this->em->getRepository(StudentGroup::class)->find($command->groupId);
        if (!$group || $group->getSchool()->getId() !== $command->schoolId || $group->getSchool()->getOwner() !== $command->owner) {
            throw new \DomainException('Turma nao encontrada.');
        }

        $this->em->remove($group);
        $this->em->flush();
    }
}
