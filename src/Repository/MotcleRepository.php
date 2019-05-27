<?php

namespace App\Repository;

use App\Entity\Motcle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Motcle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motcle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motcle[]    findAll()
 * @method Motcle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotcleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Motcle::class);
    }

    // /**
    //  * @return Motcle[] Returns an array of Motcle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Motcle
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
