<?php

declare(strict_types=1);

namespace App\Service\Handler\School;

use App\Service\Handler\QueryHandlerInterface;
use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;

final class ListSchoolsHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(ListSchoolsQuery $query): array
    {
        $schools = $this->em->getRepository(School::class)->findBy(['owner' => $query->owner]);

        return array_map(fn(School $s) => [
            'id' => $s->getId(),
            'name' => $s->getName(),
            'city' => $s->getCity(),
            'state' => $s->getState(),
            'inepCode' => $s->getInepCode(),
            'studentGroupCount' => $s->getStudentGroups()->count(),
        ], $schools);
    }
}
