<?php

namespace App\Repository;

use App\Entity\Bracket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Bracket>
 *
 * @method Bracket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bracket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bracket[]    findAll()
 * @method Bracket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BracketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bracket::class);
    }

//    /**
//     * @return Bracket[] Returns an array of Bracket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Bracket
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
