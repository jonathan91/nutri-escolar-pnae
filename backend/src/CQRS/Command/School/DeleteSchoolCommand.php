<?php

declare(strict_types=1);

namespace App\CQRS\Command\School;

use App\CQRS\Command\CommandInterface;
use App\Entity\User;

final readonly class DeleteSchoolCommand implements CommandInterface
{
    public function __construct(
        public int $schoolId,
        public User $owner,
    ) {}
}
