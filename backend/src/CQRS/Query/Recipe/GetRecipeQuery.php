<?php

declare(strict_types=1);

namespace App\CQRS\Query\Recipe;

use App\CQRS\Query\QueryInterface;
use App\Entity\User;

final readonly class GetRecipeQuery implements QueryInterface
{
    public function __construct(
        public int $recipeId,
        public User $owner,
    ) {}
}
