<?php

declare(strict_types=1);

namespace App\CQRS\Query\Dashboard;

use App\CQRS\Query\QueryHandlerInterface;
use App\Entity\Menu;
use App\Entity\Recipe;
use App\Entity\School;
use Doctrine\ORM\EntityManagerInterface;

final class GetDashboardHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(GetDashboardQuery $query): array
    {
        return [
            'schools' => $this->em->getRepository(School::class)->count(['owner' => $query->owner]),
            'menus' => $this->em->getRepository(Menu::class)->count(['owner' => $query->owner]),
            'recipes' => $this->em->getRepository(Recipe::class)->count(['owner' => $query->owner]),
        ];
    }
}
