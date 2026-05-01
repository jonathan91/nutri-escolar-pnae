<?php

declare(strict_types=1);

namespace App\Service\Command\Menu;

use App\Service\Command\CommandInterface;
use App\Entity\User;

final readonly class CreateMenuCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $menuDate,
        public string $mealType,
        public int $schoolId,
        public int $studentGroupId,
        public User $owner,
        public array $items = [],
    ) {}
}
