<?php

declare(strict_types=1);

namespace App\Service\Handler\Recipe;

use App\Service\Handler\QueryHandlerInterface;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;

final class ListRecipesHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(ListRecipesQuery $query): array
    {
        $recipes = $this->em->getRepository(Recipe::class)->findBy(['owner' => $query->owner]);

        return array_map(fn(Recipe $r) => [
            'id' => $r->getId(),
            'name' => $r->getName(),
            'portions' => $r->getPortions(),
            'costPerPortion' => $r->getCostPerPortion(),
            'ingredientCount' => $r->getIngredients()->count(),
            'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $recipes);
    }
}
