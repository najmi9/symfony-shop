<?php

namespace App\Repository;

use App\Entity\IsEnabled;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method IsEnabled|null find($id, $lockMode = null, $lockVersion = null)
 * @method IsEnabled|null findOneBy(array $criteria, array $orderBy = null)
 * @method IsEnabled[]    findAll()
 * @method IsEnabled[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IsEnabledRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IsEnabled::class);
    }

    // /**
    //  * @return IsEnabled[] Returns an array of IsEnabled objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IsEnabled
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
