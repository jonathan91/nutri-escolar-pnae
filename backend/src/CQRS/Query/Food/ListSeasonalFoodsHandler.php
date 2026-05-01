<?php

declare(strict_types=1);

namespace App\CQRS\Query\Food;

use App\CQRS\Query\QueryHandlerInterface;
use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;

final class ListSeasonalFoodsHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(ListSeasonalFoodsQuery $query): array
    {
        $foods = $this->em->getRepository(Food::class)->findAll();

        $seasonal = array_filter($foods, function (Food $f) use ($query) {
            $months = $f->getSeasonMonths();
            return $months && in_array($query->month, $months);
        });

        return array_map(fn(Food $f) => [
            'id' => $f->getId(),
            'tacoId' => $f->getTacoId(),
            'name' => $f->getName(),
            'category' => $f->getCategory(),
            'source' => $f->getSource(),
            'energy' => $f->getEnergy(),
            'protein' => $f->getProtein(),
            'carbohydrate' => $f->getCarbohydrate(),
            'lipid' => $f->getLipid(),
            'fiber' => $f->getFiber(),
            'calcium' => $f->getCalcium(),
            'iron' => $f->getIron(),
            'magnesium' => $f->getMagnesium(),
            'zinc' => $f->getZinc(),
            'vitaminA' => $f->getVitaminA(),
            'vitaminC' => $f->getVitaminC(),
            'sodium' => $f->getSodium(),
            'saturatedFat' => $f->getSaturatedFat(),
            'ultraProcessed' => $f->isUltraProcessed(),
            'containsGluten' => $f->isContainsGluten(),
            'containsLactose' => $f->isContainsLactose(),
            'allergens' => $f->getAllergens(),
            'seasonMonths' => $f->getSeasonMonths(),
        ], array_values($seasonal));
    }
}
