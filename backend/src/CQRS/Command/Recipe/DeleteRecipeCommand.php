<?php

declare(strict_types=1);

namespace App\CQRS\Command\Recipe;

use App\CQRS\Command\CommandInterface;
use App\Entity\User;

final readonly class DeleteRecipeCommand implements CommandInterface
{
    public function __construct(
        public int $recipeId,
        public User $owner,
    ) {}
}
