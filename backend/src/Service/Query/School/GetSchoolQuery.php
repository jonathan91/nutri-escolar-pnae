<?php

declare(strict_types=1);

namespace App\Service\Query\School;

use App\Service\Query\QueryInterface;
use App\Entity\User;

final readonly class GetSchoolQuery implements QueryInterface
{
    public function __construct(
        public int $schoolId,
        public User $owner,
    ) {}
}
