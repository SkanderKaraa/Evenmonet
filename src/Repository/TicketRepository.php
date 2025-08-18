<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 *
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function hasUserBoughtTicket($user, $annonce): bool
    {
        return (bool) $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.user = :userId')
            ->andWhere('t.annonce = :annonceId')
            ->setParameter('userId', $user->getId())
            ->setParameter('annonceId', $annonce->getId())
            ->getQuery()
            ->getSingleScalarResult();
    }

    // Tu peux ajouter ici des méthodes personnalisées par exemple :
    // public function findTicketsByUser($user)
    // {
    //     return $this->createQueryBuilder('t')
    //         ->andWhere('t.user = :user')
    //         ->setParameter('user', $user)
    //         ->orderBy('t.purchasedAt', 'DESC')
    //         ->getQuery()
    //         ->getResult();
    // }
}
