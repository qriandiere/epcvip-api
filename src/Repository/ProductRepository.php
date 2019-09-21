<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param string $status
     * @param \DateTime $updatedAt
     * @return Product[]
     */
    public function findByStatusAndUpdatedAt(
        string $status, \DateTime $updatedAt
    )
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->andWhere('p.updatedAt >= :updated_at')
            ->setParameters([
                'status' => $status,
                'updated_at' => $updatedAt
            ])
            ->getQuery()
            ->getResult();
    }
}