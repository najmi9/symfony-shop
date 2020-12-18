<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
     * @return Product[] Returns an array of Product objects
     */
    public function findByName(string $name=null, float $min=null, float $max=null, array $categories=[]): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($name) {
            $qb->andWhere('p.name LIKE :val')->setParameter('val', "%".$name."%");
        }

        if ($min) {
            $qb->andWhere('p.price >= :min')->setParameter('min', $min);
        }

        if ($max) {
            $qb->andWhere('p.price <= :max')->setParameter('max', $max);
        }

        if ($categories) {
            $qb->join('p.category', 'c')
                ->andWhere($qb->expr()->in('c.id', $categories));
            ;
        }
           

        return $qb->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
