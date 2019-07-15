<?php

namespace App\Repository;

use App\Entity\Acte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Acte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Acte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Acte[]    findAll()
 * @method Acte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Acte::class);
    }

    // /**
    //  * @return Acte[] Returns an array of Acte objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Acte
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


    // /**
    //  * @return Acte[] Returns an array of Acte objects 
    //  */
    
    // permet de selectionner les actes enregistrés comme brouillon
    // public function findByActeBrouillons()
    // {
    //     return $this->createQueryBuilder('a')
    //         ->andWhere('a.fkEtat = :val')
    //         ->setParameter('val', 1)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }
    
}
