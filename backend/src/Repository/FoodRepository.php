<?php

namespace App\Repository;

use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Food::class);
    }

    public function search(string $query, ?string $category = null, ?int $month = null): array
    {
        $qb = $this->createQueryBuilder('f')
            ->andWhere('LOWER(f.name) LIKE :query')
            ->setParameter('query', '%' . strtolower($query) . '%')
            ->orderBy('f.name', 'ASC')
            ->setMaxResults(50);

        if ($category) {
            $qb->andWhere('f.category = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.category = :category')
            ->setParameter('category', $category)
            ->orderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCategories(): array
    {
        return $this->createQueryBuilder('f')
            ->select('DISTINCT f.category')
            ->orderBy('f.category', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }
}
