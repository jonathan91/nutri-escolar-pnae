<?php

declare(strict_types=1);

namespace App\CQRS\Query\Menu;

use App\CQRS\Query\QueryInterface;
use App\Entity\User;

final readonly class ListMenusQuery implements QueryInterface
{
    public function __construct(
        public User $owner,
    ) {}
}
