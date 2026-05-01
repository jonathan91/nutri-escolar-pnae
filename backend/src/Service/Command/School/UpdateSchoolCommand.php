<?php

declare(strict_types=1);

namespace App\Service\Command\School;

use App\Service\Command\CommandInterface;
use App\Entity\User;

final readonly class UpdateSchoolCommand implements CommandInterface
{
    public function __construct(
        public int $schoolId,
        public User $owner,
        public ?string $name = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $inepCode = null,
    ) {}
}
