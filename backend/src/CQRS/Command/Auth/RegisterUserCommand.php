<?php

declare(strict_types=1);

namespace App\CQRS\Command\Auth;

use App\CQRS\Command\CommandInterface;

final readonly class RegisterUserCommand implements CommandInterface
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public ?string $crn = null,
    ) {}
}
