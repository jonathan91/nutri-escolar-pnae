<?php

declare(strict_types=1);

namespace App\CQRS\Command\Menu;

use App\CQRS\Command\CommandInterface;
use App\Entity\User;

final readonly class DeleteMenuCommand implements CommandInterface
{
    public function __construct(
        public int $menuId,
        public User $owner,
    ) {}
}
