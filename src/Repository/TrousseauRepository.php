<?php

namespace App\Repository;

use App\Entity\Trousseau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Trousseau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trousseau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trousseau[]    findAll()
 * @method Trousseau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrousseauRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Trousseau::class);
    }
    
    public function getListKeys()
    {
		 $qb = $this->createQueryBuilder('l')
            //->andWhere('p.type = 1')
            ->orderBy('l.modele', 'ASC')
            ->getQuery();

        return $qb->execute();
    }

    public function listTrousseauPerSite() {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select t.site, COUNT(t.site) FROM trousseau t group BY t.site';
        //$sql = 'select t.* FROM trousseau t';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function listStatePerSite() {
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'select t.state, COUNT(t.state) FROM trousseau t group BY t.state';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
	
    public function getListFreeKeyWithCondition($type, $site)
    {
		// SELECT t.* FROM trousseau t LEFT OUTER JOIN pret p ON p.trousseau_id=t.id WHERE p.trousseau_id IS NULL AND t.type = 2
		 $conn = $this->getEntityManager()->getConnection();
		 
		 $sql = 'SELECT t.* FROM trousseau t LEFT OUTER JOIN pret p ON p.trousseau_id=t.id WHERE p.trousseau_id IS NULL AND t.type = :type AND t.site = :site';
		 $stmt = $conn->prepare($sql);
         $stmt->execute(array('type' => $type, 'site' => $site));
		 return $stmt->fetchAll();
	}
	
    public function getListKeyWithCondition($type, $site)
    {
		 $qb = $this->createQueryBuilder('l')
            ->andWhere('l.type = (:type) AND l.site = (:site)')
            ->setParameter('type', $type)
            ->setParameter('site', $site)
            ->orderBy('l.modele', 'ASC')
            ->getQuery();

        return $qb->execute();
	}

    // /**
    //  * @return Trousseau[] Returns an array of Trousseau objects
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
    public function findOneBySomeField($value): ?Trousseau
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

 
