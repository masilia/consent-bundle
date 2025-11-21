<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Masilia\ConsentBundle\Entity\ThirdPartyServiceTranslation;

/**
 * @extends ServiceEntityRepository<ThirdPartyServiceTranslation>
 */
class ThirdPartyServiceTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThirdPartyServiceTranslation::class);
    }

    public function save(ThirdPartyServiceTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ThirdPartyServiceTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
