<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findByOwnerAndDateRange(int $ownerId, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.owner = :ownerId')
            ->andWhere('m.menuDate >= :start')
            ->andWhere('m.menuDate <= :end')
            ->setParameter('ownerId', $ownerId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('m.menuDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByOwner(int $ownerId): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('m.menuDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
