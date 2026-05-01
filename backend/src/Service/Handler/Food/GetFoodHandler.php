<?php

declare(strict_types=1);

namespace App\Service\Handler\Food;

use App\Service\Handler\QueryHandlerInterface;
use App\Entity\Food;
use Doctrine\ORM\EntityManagerInterface;

final class GetFoodHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(GetFoodQuery $query): ?array
    {
        $food = $this->em->getRepository(Food::class)->find($query->foodId);
        if (!$food) {
            return null;
        }

        return [
            'id' => $food->getId(),
            'tacoId' => $food->getTacoId(),
            'name' => $food->getName(),
            'category' => $food->getCategory(),
            'source' => $food->getSource(),
            'energy' => $food->getEnergy(),
            'protein' => $food->getProtein(),
            'carbohydrate' => $food->getCarbohydrate(),
            'lipid' => $food->getLipid(),
            'fiber' => $food->getFiber(),
            'calcium' => $food->getCalcium(),
            'iron' => $food->getIron(),
            'magnesium' => $food->getMagnesium(),
            'zinc' => $food->getZinc(),
            'vitaminA' => $food->getVitaminA(),
            'vitaminC' => $food->getVitaminC(),
            'sodium' => $food->getSodium(),
            'saturatedFat' => $food->getSaturatedFat(),
            'ultraProcessed' => $food->isUltraProcessed(),
            'containsGluten' => $food->isContainsGluten(),
            'containsLactose' => $food->isContainsLactose(),
            'allergens' => $food->getAllergens(),
            'seasonMonths' => $food->getSeasonMonths(),
        ];
    }
}
