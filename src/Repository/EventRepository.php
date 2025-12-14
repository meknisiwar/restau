<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Find upcoming published events (from now onward)
     * @param int $limit
     * @return Event[]
     */
    public function findUpcoming(int $limit = 20): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.published = true')
            ->andWhere('e.startAt >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.startAt', 'ASC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find upcoming events and include RSVP counts for each event.
     * Returns array of [ 'event' => Event, 'rsvpCount' => int ]
     */
    public function findUpcomingWithRsvpCount(int $limit = 20): array
    {
        $dql = "SELECT e, COUNT(r.id) AS rsvpCount
                FROM App\\Entity\\Event e
                LEFT JOIN App\\Entity\\Rsvp r WITH r.event = e
                WHERE e.published = true AND e.startAt >= :now
                GROUP BY e.id
                ORDER BY e.startAt ASC";

        $query = $this->_em->createQuery($dql)
            ->setParameter('now', new \DateTimeImmutable())
            ->setMaxResults($limit);

        $rows = $query->getResult();

        // normalize to consistent structure
        $results = [];
        foreach ($rows as $row) {
            // Doctrine returns either [0 => Event, 'rsvpCount' => 'N'] or Event object depending on hydration
            if (is_array($row)) {
                $results[] = ['event' => $row[0], 'rsvpCount' => (int)$row['rsvpCount']];
            } elseif ($row instanceof Event) {
                // fallback if rsvpCount not present
                $results[] = ['event' => $row, 'rsvpCount' => 0];
            }
        }

        return $results;
    }

    /**
     * Find past events (ended before now)
     * @param int $limit
     * @return Event[]
     */
    public function findPast(int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.published = true')
            ->andWhere('e.startAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.startAt', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find events within a given month
     * @return Event[]
     */
    public function findByMonth(int $year, int $month): array
    {
        $start = new \DateTimeImmutable("{$year}-{$month}-01 00:00:00");
        $end = $start->modify('first day of next month');

        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.published = true')
            ->andWhere('e.startAt >= :start')
            ->andWhere('e.startAt < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('e.startAt', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
