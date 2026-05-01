<?php

declare(strict_types=1);

namespace App\CQRS\Command\School;

use App\CQRS\Command\CommandInterface;
use App\Entity\User;

final readonly class DeleteStudentGroupCommand implements CommandInterface
{
    public function __construct(
        public int $schoolId,
        public int $groupId,
        public User $owner,
    ) {}
}
