<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\CookiePolicy;

/**
 * @extends ServiceEntityRepository<CookieCategory>
 */
class CookieCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CookieCategory::class);
    }

    public function findByPolicy(CookiePolicy $policy): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.policy = :policy')
            ->setParameter('policy', $policy)
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByIdentifier(string $identifier, CookiePolicy $policy): ?CookieCategory
    {
        return $this->findOneBy([
            'identifier' => $identifier,
            'policy' => $policy,
        ]);
    }

    public function findRequiredCategories(CookiePolicy $policy): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.policy = :policy')
            ->andWhere('c.required = :required')
            ->setParameter('policy', $policy)
            ->setParameter('required', true)
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(CookieCategory $category, bool $flush = false): void
    {
        $this->getEntityManager()->persist($category);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CookieCategory $category, bool $flush = false): void
    {
        $this->getEntityManager()->remove($category);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
