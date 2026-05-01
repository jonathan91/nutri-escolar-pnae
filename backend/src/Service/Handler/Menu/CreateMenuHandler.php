<?php

declare(strict_types=1);

namespace App\Service\Handler\Menu;

use App\Service\Handler\CommandHandlerInterface;
use App\Entity\Food;
use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\Recipe;
use App\Entity\School;
use App\Entity\StudentGroup;
use Doctrine\ORM\EntityManagerInterface;

final class CreateMenuHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(CreateMenuCommand $command): Menu
    {
        $school = $this->em->getRepository(School::class)->find($command->schoolId);
        $studentGroup = $this->em->getRepository(StudentGroup::class)->find($command->studentGroupId);

        if (!$school || $school->getOwner() !== $command->owner) {
            throw new \DomainException('Escola nao encontrada.');
        }
        if (!$studentGroup) {
            throw new \DomainException('Turma nao encontrada.');
        }

        $menu = new Menu();
        $menu->setName($command->name);
        $menu->setMenuDate(new \DateTime($command->menuDate));
        $menu->setMealType($command->mealType);
        $menu->setSchool($school);
        $menu->setStudentGroup($studentGroup);
        $menu->setOwner($command->owner);

        foreach ($command->items as $itemData) {
            $menuItem = new MenuItem();
            $menuItem->setPortionSize($itemData['portionSize'] ?? 100);
            $menuItem->setServings($itemData['servings'] ?? 1);

            if (!empty($itemData['foodId'])) {
                $food = $this->em->getRepository(Food::class)->find($itemData['foodId']);
                $menuItem->setFood($food);
            }
            if (!empty($itemData['recipeId'])) {
                $recipe = $this->em->getRepository(Recipe::class)->find($itemData['recipeId']);
                $menuItem->setRecipe($recipe);
            }

            $menu->addItem($menuItem);
        }

        $this->em->persist($menu);
        $this->em->flush();

        return $menu;
    }
}
