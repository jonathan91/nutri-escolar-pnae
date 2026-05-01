<?php

declare(strict_types=1);

namespace App\Service\Command\Menu;

use App\Service\Command\CommandInterface;
use App\Entity\User;

final readonly class DeleteMenuCommand implements CommandInterface
{
    public function __construct(
        public int $menuId,
        public User $owner,
    ) {}
}
