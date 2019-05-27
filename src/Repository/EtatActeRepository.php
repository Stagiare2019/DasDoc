<?php

namespace App\Repository;

use App\Entity\EtatActe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EtatActe|null find($id, $lockMode = null, $lockVersion = null)
 * @method EtatActe|null findOneBy(array $criteria, array $orderBy = null)
 * @method EtatActe[]    findAll()
 * @method EtatActe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EtatActeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EtatActe::class);
    }

    // /**
    //  * @return EtatActe[] Returns an array of EtatActe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EtatActe
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
