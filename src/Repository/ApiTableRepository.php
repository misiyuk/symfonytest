<?php

namespace App\Repository;

use App\Entity\ApiTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ApiTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method ApiTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method ApiTable[]    findAll()
 * @method ApiTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApiTableRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ApiTable::class);
    }

    // /**
    //  * @return Table[] Returns an array of Table objects
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
    public function findOneBySomeField($value): ?Table
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
