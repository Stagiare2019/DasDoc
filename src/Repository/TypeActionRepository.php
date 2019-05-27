<?php

namespace App\Repository;

use App\Entity\TypeAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TypeAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeAction[]    findAll()
 * @method TypeAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeActionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TypeAction::class);
    }

    // /**
    //  * @return TypeAction[] Returns an array of TypeAction objects
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
    public function findOneBySomeField($value): ?TypeAction
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
