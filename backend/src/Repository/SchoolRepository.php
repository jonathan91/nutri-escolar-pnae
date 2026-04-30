<?php

namespace App\Repository;

use App\Entity\School;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SchoolRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, School::class);
    }

    public function findByOwner(int $ownerId): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
