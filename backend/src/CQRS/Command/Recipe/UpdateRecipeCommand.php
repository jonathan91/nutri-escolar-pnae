<?php

declare(strict_types=1);

namespace App\CQRS\Command\Recipe;

use App\CQRS\Command\CommandInterface;
use App\Entity\User;

final readonly class UpdateRecipeCommand implements CommandInterface
{
    public function __construct(
        public int $recipeId,
        public User $owner,
        public ?string $name = null,
        public ?string $preparationMethod = null,
        public ?int $portions = null,
        public ?array $ingredients = null,
    ) {}
}
