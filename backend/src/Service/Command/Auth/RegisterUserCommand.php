<?php

declare(strict_types=1);

namespace App\Service\Command\Auth;

use App\Service\Command\CommandInterface;

final readonly class RegisterUserCommand implements CommandInterface
{
    public function __construct(
        public string $email,
        public string $name,
        public string $password,
        public ?string $crn = null,
    ) {}
}
