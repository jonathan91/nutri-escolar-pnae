<?php

declare(strict_types=1);

namespace App\Service\Query\School;

use App\Service\Query\QueryInterface;
use App\Entity\User;

final readonly class ListSchoolsQuery implements QueryInterface
{
    public function __construct(
        public User $owner,
    ) {}
}
