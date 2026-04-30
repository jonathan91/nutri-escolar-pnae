<?php

declare(strict_types=1);

namespace App\CQRS\Query\Dashboard;

use App\CQRS\Query\QueryInterface;

final readonly class GetReferencesQuery implements QueryInterface
{
    public function __construct(
        public string $ageGroup = 'fundamental_6_10',
        public string $mealPeriod = 'parcial_20',
    ) {}
}
