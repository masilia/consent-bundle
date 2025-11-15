<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Masilia\ConsentBundle\Entity\Cookie;
use Masilia\ConsentBundle\Entity\CookieCategory;

/**
 * @extends ServiceEntityRepository<Cookie>
 */
class CookieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cookie::class);
    }

    public function findByCategory(CookieCategory $category): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.category = :category')
            ->setParameter('category', $category)
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findWithScripts(CookieCategory $category): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.category = :category')
            ->andWhere('c.scriptSrc IS NOT NULL OR c.initCode IS NOT NULL')
            ->setParameter('category', $category)
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Cookie $cookie, bool $flush = false): void
    {
        $this->getEntityManager()->persist($cookie);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cookie $cookie, bool $flush = false): void
    {
        $this->getEntityManager()->remove($cookie);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
