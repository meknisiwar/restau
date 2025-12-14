<?php

namespace App\Repository;

use App\Entity\Rsvp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RsvpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rsvp::class);
    }

    public function countForEvent($eventId): int
    {
        return (int)$this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.event = :e')
            ->setParameter('e', $eventId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByUserAndEvent($userId, $eventId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :u')
            ->andWhere('r.event = :e')
            ->setParameter('u', $userId)
            ->setParameter('e', $eventId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
