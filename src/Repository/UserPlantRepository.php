<?php

namespace App\Repository;

use App\Entity\UserPlant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method UserPlant|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPlant|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPlant[]    findAll()
 * @method UserPlant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPlantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserPlant::class);
    }

    // /**
    //  * @return UserPlant[] Returns an array of UserPlant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPlant
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
