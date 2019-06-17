<?php

namespace App\Repository;

use App\Entity\Stay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Stay|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stay[]    findAll()
 * @method Stay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StayRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Stay::class);
    }

    public function byUser($userId) {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.user = :user')
            ->setParameter('user', $userId)->orderBy('s.arrival', 'ASC')
            ->getQuery();
        return $qb->execute();
    }


    public function getDistinctYear() {
        $fields = array('s.arrival', 's.departure');
        $qb = $this->createQueryBuilder('s')->select($fields)->distinct(true)->orderBy('s.arrival')->getQuery();
        $a = $qb->execute();
        $result = array();
        foreach($a as $elm) {
            if ($elm != null) {
                if ($elm["arrival"]!= null && !in_array($elm["arrival"]->format('Y'), $result)) {
                    array_push($result, $elm["arrival"]->format('Y'));
                }
                if ($elm["departure"]!= null && !in_array($elm["departure"]->format('Y'), $result)) {
                    array_push($result, $elm["departure"]->format('Y'));
                }
            }
        }
        return $result;
    }

    // /**
    //  * @return Stay[] Returns an array of Stay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Stay
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
