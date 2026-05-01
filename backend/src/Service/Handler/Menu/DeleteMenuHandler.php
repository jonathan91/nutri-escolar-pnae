<?php

declare(strict_types=1);

namespace App\Service\Handler\Menu;

use App\Service\Command\Menu\DeleteMenuCommand;
use App\Service\Handler\CommandHandlerInterface;
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
