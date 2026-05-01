<?php

declare(strict_types=1);

namespace App\CQRS\Command\Menu;

use App\CQRS\Command\CommandInterface;
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
