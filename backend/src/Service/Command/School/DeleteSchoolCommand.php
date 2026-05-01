<?php

declare(strict_types=1);

namespace App\Service\Command\School;

use App\Service\Command\CommandInterface;
use App\Entity\User;

final readonly class DeleteSchoolCommand implements CommandInterface
{
    public function __construct(
        public int $schoolId,
        public User $owner,
    ) {}
}
