<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Masilia\ConsentBundle\Entity\CookiePolicy;

/**
 * @extends ServiceEntityRepository<CookiePolicy>
 */
class CookiePolicyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CookiePolicy::class);
    }

    public function findActivePolicy(): ?CookiePolicy
    {
        return $this->findOneBy(['isActive' => true]);
    }

    public function findByVersion(string $version): ?CookiePolicy
    {
        return $this->findOneBy(['version' => $version]);
    }

    public function deactivateAll(): void
    {
        $this->createQueryBuilder('p')
            ->update()
            ->set('p.isActive', ':active')
            ->setParameter('active', false)
            ->getQuery()
            ->execute();
    }

    public function save(CookiePolicy $policy, bool $flush = false): void
    {
        $this->getEntityManager()->persist($policy);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CookiePolicy $policy, bool $flush = false): void
    {
        $this->getEntityManager()->remove($policy);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
