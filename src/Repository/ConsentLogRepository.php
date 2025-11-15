<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Masilia\ConsentBundle\Entity\ConsentLog;

/**
 * @extends ServiceEntityRepository<ConsentLog>
 */
class ConsentLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsentLog::class);
    }

    public function findBySessionId(string $sessionId): ?ConsentLog
    {
        return $this->createQueryBuilder('l')
            ->where('l.sessionId = :sessionId')
            ->setParameter('sessionId', $sessionId)
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserId(int $userId): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('l.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getConsentStatistics(\DateTimeInterface $from, \DateTimeInterface $to): array
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l.preferences, COUNT(l.id) as count')
            ->where('l.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->groupBy('l.preferences')
            ->getQuery();

        return $qb->getResult();
    }

    public function getTotalConsents(\DateTimeInterface $from, \DateTimeInterface $to): int
    {
        return (int) $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.createdAt BETWEEN :from AND :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function save(ConsentLog $log, bool $flush = false): void
    {
        $this->getEntityManager()->persist($log);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
