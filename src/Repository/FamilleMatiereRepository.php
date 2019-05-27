<?php

namespace App\Repository;

use App\Entity\FamilleMatiere;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method FamilleMatiere|null find($id, $lockMode = null, $lockVersion = null)
 * @method FamilleMatiere|null findOneBy(array $criteria, array $orderBy = null)
 * @method FamilleMatiere[]    findAll()
 * @method FamilleMatiere[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FamilleMatiereRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, FamilleMatiere::class);
    }

    // /**
    //  * @return FamilleMatiere[] Returns an array of FamilleMatiere objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FamilleMatiere
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
