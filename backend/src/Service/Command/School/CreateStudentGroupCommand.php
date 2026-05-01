<?php

declare(strict_types=1);

namespace App\Service\Command\School;

use App\Service\Command\CommandInterface;
use App\Entity\User;

final readonly class CreateStudentGroupCommand implements CommandInterface
{
    public function __construct(
        public int $schoolId,
        public string $name,
        public string $ageGroup,
        public string $mealPeriod,
        public int $studentCount,
        public User $owner,
    ) {}
}
