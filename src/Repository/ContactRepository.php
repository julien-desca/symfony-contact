<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function getContactByUser(User  $user){
        return $this->createQueryBuilder('c')
            ->leftJoin('c.groupes', 'g')
            ->andWhere('g.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function search(?string $searchTerm)
    {
        $qb = $this->createQueryBuilder("c");
        $qb->andWhere($qb->expr()->like('c.prenom', "'%$searchTerm%'"));
        $qb->orWhere($qb->expr()->like('c.nom', "'%$searchTerm%'"));
        return $qb->getQuery()->getResult();
    }
    // /**
    //  * @return Contact[] Returns an array of Contact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
