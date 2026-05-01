<?php

declare(strict_types=1);

namespace App\CQRS\Query\Food;

use App\CQRS\Query\QueryHandlerInterface;
use App\Entity\Food;
use App\Repository\FoodRepository;

final class ListFoodsHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FoodRepository $foodRepo,
    ) {}

    public function __invoke(ListFoodsQuery $query): array
    {
        if (strlen($query->search) >= 2) {
            $foods = $this->foodRepo->search($query->search, $query->category, $query->month);
        } elseif ($query->category) {
            $foods = $this->foodRepo->findByCategory($query->category);
        } else {
            $foods = $this->foodRepo->findBy([], ['name' => 'ASC'], 100);
        }

        return array_map(fn(Food $f) => $this->serialize($f), $foods);
    }

    private function serialize(Food $f): array
    {
        return [
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
        ];
    }
}
