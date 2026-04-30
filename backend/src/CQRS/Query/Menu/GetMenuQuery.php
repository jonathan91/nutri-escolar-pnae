<?php

declare(strict_types=1);

namespace App\CQRS\Query\Menu;

use App\CQRS\Query\QueryInterface;
use App\Entity\User;

final readonly class GetMenuQuery implements QueryInterface
{
    public function __construct(
        public int $menuId,
        public User $owner,
    ) {}
}
