<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findByOwner(int $ownerId): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.owner = :ownerId')
            ->setParameter('ownerId', $ownerId)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
