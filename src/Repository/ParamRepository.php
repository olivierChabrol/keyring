<?php

namespace App\Repository;

use App\Entity\Param;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Param|null find($id, $lockMode = null, $lockVersion = null)
 * @method Param|null findOneBy(array $criteria, array $orderBy = null)
 * @method Param[]    findAll()
 * @method Param[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParamRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Param::class);
    }

    public function getParamByType($t)
    {
		 $qb = $this->createQueryBuilder('p')
            ->andWhere('p.type = (:type)')
            ->setParameter('type', $t)
            ->orderBy('p.value', 'ASC')
            ->getQuery();

        return $qb->execute();

    }
    
    public function getDepartment()
    {
        return $this->getParamByType(Param::DEPARTMENT);
    }

    public function getKeyState()
    {
    return $this->getParamByType(3);
    }
    public function getKeyType()
    {
		 return $this->getParamByType(1);
	}


	public function getKeySite()
	{
		 return $this->getParamByType(2);
	}

	public function getAssociativeArrayParam($typeParam = null)
	{
        $qb = $this->createQueryBuilder('p');
        if (null !== $typeParam) {
            $qb->andWhere('p.type = (:type)')->setParameter('type', $typeParam);
        }
        $qb = $qb->orderBy('p.value', 'ASC')->getQuery();

        $params = $qb->execute();
        $assocArray = array();
		foreach( $params as $param ) {
			$assocArray[$param->getId()] = $param->getValue();
		}

		return $assocArray;
    }

    // /**
    //  * @return Param[] Returns an array of Param objects
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
    public function findOneBySomeField($value): ?Param
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
