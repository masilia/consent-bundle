<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Entity\ThirdPartyService;

/**
 * @extends ServiceEntityRepository<ThirdPartyService>
 */
class ThirdPartyServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThirdPartyService::class);
    }

    public function findByPolicy(CookiePolicy $policy): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.policy = :policy')
            ->setParameter('policy', $policy)
            ->getQuery()
            ->getResult();
    }

    public function findEnabledByCategory(string $category, CookiePolicy $policy): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.policy = :policy')
            ->andWhere('s.category = :category')
            ->andWhere('s.enabled = :enabled')
            ->setParameter('policy', $policy)
            ->setParameter('category', $category)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }

    public function findByIdentifier(string $identifier, CookiePolicy $policy): ?ThirdPartyService
    {
        return $this->findOneBy([
            'identifier' => $identifier,
            'policy' => $policy,
        ]);
    }

    public function save(ThirdPartyService $service, bool $flush = false): void
    {
        $this->getEntityManager()->persist($service);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ThirdPartyService $service, bool $flush = false): void
    {
        $this->getEntityManager()->remove($service);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
