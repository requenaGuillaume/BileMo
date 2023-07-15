<?php

namespace App\Repository;

use App\Entity\SelfDiscoverability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SelfDiscoverability>
 *
 * @method SelfDiscoverability|null find($id, $lockMode = null, $lockVersion = null)
 * @method SelfDiscoverability|null findOneBy(array $criteria, array $orderBy = null)
 * @method SelfDiscoverability[]    findAll()
 * @method SelfDiscoverability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SelfDiscoverabilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SelfDiscoverability::class);
    }

    public function save(SelfDiscoverability $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SelfDiscoverability $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SelfDiscoverability[] Returns an array of SelfDiscoverability objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SelfDiscoverability
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
