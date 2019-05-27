<?php

namespace App\Repository;

use App\Entity\TypeMotcle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TypeMotcle|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeMotcle|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeMotcle[]    findAll()
 * @method TypeMotcle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeMotcleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TypeMotcle::class);
    }

    // /**
    //  * @return TypeMotcle[] Returns an array of TypeMotcle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeMotcle
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
