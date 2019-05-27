<?php

namespace App\Repository;

use App\Entity\NatureActe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NatureActe|null find($id, $lockMode = null, $lockVersion = null)
 * @method NatureActe|null findOneBy(array $criteria, array $orderBy = null)
 * @method NatureActe[]    findAll()
 * @method NatureActe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NatureActeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NatureActe::class);
    }

    // /**
    //  * @return NatureActe[] Returns an array of NatureActe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?NatureActe
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
