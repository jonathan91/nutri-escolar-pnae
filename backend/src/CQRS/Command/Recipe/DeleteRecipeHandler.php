<?php

declare(strict_types=1);

namespace App\CQRS\Command\Recipe;

use App\CQRS\Command\CommandHandlerInterface;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteRecipeHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(DeleteRecipeCommand $command): void
    {
        $recipe = $this->em->getRepository(Recipe::class)->find($command->recipeId);
        if (!$recipe || $recipe->getOwner() !== $command->owner) {
            throw new \DomainException('Receita nao encontrada.');
        }

        $this->em->remove($recipe);
        $this->em->flush();
    }
}
