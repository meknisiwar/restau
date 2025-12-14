<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAvailableProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.available = :available')
            ->setParameter('available', true)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategory(int $categoryId): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :categoryId')
            ->andWhere('p.available = :available')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('available', true)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search available products by query (name or description) and optional category.
     * @param string|null $q
     * @param int|null $categoryId
     * @return Product[]
     */
    public function searchAvailable(?string $q, ?int $categoryId = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.available = :available')
            ->setParameter('available', true);

        if ($categoryId) {
            $qb->andWhere('p.category = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }

        if ($q !== null && trim($q) !== '') {
            $term = mb_strtolower(trim($q));
            $qb->andWhere('LOWER(p.name) LIKE :term OR LOWER(p.description) LIKE :term')
               ->setParameter('term', '%' . $term . '%');
        }

        return $qb->orderBy('p.name', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}

