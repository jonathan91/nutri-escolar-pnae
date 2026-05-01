<?php

declare(strict_types=1);

namespace App\Service\Handler\Menu;

use App\Service\Handler\QueryHandlerInterface;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;

final class ListMenusHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(ListMenusQuery $query): array
    {
        $menus = $this->em->getRepository(Menu::class)->findBy(
            ['owner' => $query->owner],
            ['menuDate' => 'DESC']
        );

        return array_map(fn(Menu $m) => [
            'id' => $m->getId(),
            'name' => $m->getName(),
            'menuDate' => $m->getMenuDate()?->format('Y-m-d'),
            'mealType' => $m->getMealType(),
            'schoolName' => $m->getSchool()->getName(),
            'studentGroupName' => $m->getStudentGroup()->getName(),
            'itemCount' => $m->getItems()->count(),
        ], $menus);
    }
}
