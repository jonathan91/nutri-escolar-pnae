<?php

declare(strict_types=1);

namespace App\Service\Handler\Auth;

use App\Service\Command\Auth\RegisterUserCommand;
use App\Service\Handler\CommandHandlerInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegisterUserHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(RegisterUserCommand $command): User
    {
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['email' => $command->email]);
        if ($existingUser) {
            throw new \DomainException('Email ja cadastrado.');
        }

        $user = new User();
        $user->setEmail($command->email);
        $user->setName($command->name);
        $user->setCrn($command->crn);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $command->password));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
