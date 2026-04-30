<?php

declare(strict_types=1);

namespace App\CQRS\Query\Recipe;

use App\CQRS\Query\QueryInterface;
use App\Entity\User;

final readonly class ListRecipesQuery implements QueryInterface
{
    public function __construct(
        public User $owner,
    ) {}
}
