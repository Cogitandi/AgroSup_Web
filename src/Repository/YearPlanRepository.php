<?php

namespace App\Repository;

use App\Entity\YearPlan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method YearPlan|null find($id, $lockMode = null, $lockVersion = null)
 * @method YearPlan|null findOneBy(array $criteria, array $orderBy = null)
 * @method YearPlan[]    findAll()
 * @method YearPlan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YearPlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, YearPlan::class);
    }

    // /**
    //  * @return YearPlan[] Returns an array of YearPlan objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?YearPlan
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
