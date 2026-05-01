<?php

declare(strict_types=1);

namespace App\CQRS\Command\Recipe;

use App\CQRS\Command\CommandInterface;
use App\Entity\User;

final readonly class CreateRecipeCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public User $owner,
        public ?string $preparationMethod = null,
        public int $portions = 1,
        public array $ingredients = [],
    ) {}
}
