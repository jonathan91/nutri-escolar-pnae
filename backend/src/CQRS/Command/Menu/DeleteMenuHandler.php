<?php

declare(strict_types=1);

namespace App\CQRS\Command\Menu;

use App\CQRS\Command\CommandHandlerInterface;
use App\Entity\Menu;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteMenuHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(DeleteMenuCommand $command): void
    {
        $menu = $this->em->getRepository(Menu::class)->find($command->menuId);
        if (!$menu || $menu->getOwner() !== $command->owner) {
            throw new \DomainException('Cardapio nao encontrado.');
        }

        $this->em->remove($menu);
        $this->em->flush();
    }
}
