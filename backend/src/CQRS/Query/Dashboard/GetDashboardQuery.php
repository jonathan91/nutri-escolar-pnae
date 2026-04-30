<?php

declare(strict_types=1);

namespace App\CQRS\Query\Dashboard;

use App\CQRS\Query\QueryInterface;
use App\Entity\User;

final readonly class GetDashboardQuery implements QueryInterface
{
    public function __construct(
        public User $owner,
    ) {}
}
