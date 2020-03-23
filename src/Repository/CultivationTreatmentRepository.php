<?php

namespace App\Repository;

use App\Entity\CultivationTreatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CultivationTreatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method CultivationTreatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method CultivationTreatment[]    findAll()
 * @method CultivationTreatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CultivationTreatmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CultivationTreatment::class);
    }

    // /**
    //  * @return CultivationTreatment[] Returns an array of CultivationTreatment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CultivationTreatment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
