<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

//use DoctrineExtensions\Query\Mysql\Year;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
    
    /**
     * @param $filters : associative array (table column name, value)
     */
    public function getUsers($filters) {
        $qb = $this->createQueryBuilder('u');
        $em = $this->getEntityManager()->getConfiguration();
        $em->addCustomDateTimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        
        if ($filters != NULL) {
            foreach ($filters as $k => $v) {
                if ($k != 'year') {
                    $qb = $qb->andWhere('u.'.$k.' = :'.$k);
                    $qb = $qb->setParameter($k,$v);
                }
                else {
                    $qb->innerJoin('u.stays', 's');
                    $qb = $qb->andWhere('YEAR(s.arrival) = :'.$k.' OR YEAR(s.departure) = :'.$k);
                    $qb = $qb->setParameter($k,$v);
                }
            }
        }
        $qb->orderBy('u.name', 'ASC');
        //dump($qb);die();
        $qb = $qb->getQuery();
        
        return $qb->execute();
    }

    public function getByMail($email) {
        $qb = $this->createQueryBuilder('u');
        $qb = $qb->andWhere('u.email = :email');
        $qb = $qb->setParameter('email',$email);
        $qb = $qb->getQuery();
        return $qb->execute();
    }

    public function getDistinctYear() {
        $fields = array('u.arrival', 'u.departure');
        $qb = $this->createQueryBuilder('u')->select($fields)->distinct(true)->orderBy('u.arrival')->getQuery();
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

    public function getUserEmail()
    {
		$fields = array('u.id', 'u.email');
		 $qb = $this->createQueryBuilder('u')
		    ->select($fields)
            ->orderBy('u.email', 'ASC')
            ->getQuery();

        return $qb->execute();
    }
    public  function getUserByName($req)
    {
		$fields = array('u.id', 'u.name', 'u.first_name');
		 $qb = $this->createQueryBuilder('u')
            ->andWhere('u.name LIKE :req OR u.firstName LIKE :req')
            ->setParameter('req', '%'.$req.'%')
            ->orderBy('u.name', 'ASC')
            ->getQuery();
        return $qb->execute();
	}
	

    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
