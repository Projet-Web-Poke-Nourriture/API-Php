<?php

namespace App\Repository;

use App\Entity\Ingrédient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ingrédient>
 *
 * @method Ingrédient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingrédient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingrédient[]    findAll()
 * @method Ingrédient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngrédientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ingrédient::class);
    }

//    /**
//     * @return Ingrédient[] Returns an array of Ingrédient objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ingrédient
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
