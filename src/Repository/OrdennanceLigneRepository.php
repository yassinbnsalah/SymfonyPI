<?php

namespace App\Repository;

use App\Entity\OrdennanceLigne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrdennanceLigne>
 *
 * @method OrdennanceLigne|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrdennanceLigne|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrdennanceLigne[]    findAll()
 * @method OrdennanceLigne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdennanceLigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrdennanceLigne::class);
    }

    public function save(OrdennanceLigne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OrdennanceLigne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OrdennanceLigne[] Returns an array of OrdennanceLigne objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OrdennanceLigne
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
