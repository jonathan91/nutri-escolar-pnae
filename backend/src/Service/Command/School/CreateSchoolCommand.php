<?php

declare(strict_types=1);

namespace App\Service\Command\School;

use App\Service\Command\CommandInterface;
use App\Entity\User;

final readonly class CreateSchoolCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public User $owner,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $inepCode = null,
    ) {}
}
