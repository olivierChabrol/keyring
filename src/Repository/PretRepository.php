<?php

namespace App\Repository;

use App\Entity\Pret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pret[]    findAll()
 * @method Pret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PretRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pret::class);
    }

    public function getPretByTrousseau($trousseauId)
    {
		 $qb = $this->createQueryBuilder('p')
            ->andWhere('p.trousseau = (:trousseauId)')
            ->setParameter('trousseauId', $trousseauId)
            ->getQuery();
        return $qb->execute();
	}

    public function getPretByUser($userId)
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.user = (:userId)')
            ->setParameter('userId', $userId)
            ->getQuery();
        return $qb->execute();
    }

    public function listExpiralLend($date0, $date24)
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.end BETWEEN (:date0) AND (:date24)')
            ->setParameter('date0', $date0)
            ->setParameter('date24', $date24)
            ->getQuery();
        return $qb->execute();
    }
  

    // /**
    //  * @return Pret[] Returns an array of Pret objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pret
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
