<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Masilia\ConsentBundle\Entity\Cookie;
use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Masilia\ConsentBundle\Repository\CookieCategoryRepository;
use Masilia\ConsentBundle\Service\CookiePresetService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Automatically creates cookies when a third-party service with a preset is created
 */
#[AsEntityListener(event: Events::postPersist)]
class ThirdPartyServiceSubscriber
{
    public function __construct(
        private CookiePresetService $presetService,
        private CookieCategoryRepository $categoryRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ThirdPartyService) {
            return;
        }

        // Only process if a preset type is selected
        if (!$entity->getPresetType()) {
            return;
        }

        $this->createCookiesFromPreset($entity);
    }

    private function createCookiesFromPreset(ThirdPartyService $service): void
    {
        $presetType = $service->getPresetType();
        $preset = $this->presetService->getPreset($presetType);

        if (!$preset) {
            $this->logger->warning('Preset not found for third-party service', [
                'service_id' => $service->getId(),
                'preset_type' => $presetType,
            ]);
            return;
        }

        // Find the category for this service
        $category = $this->categoryRepository->findOneBy([
            'policy' => $service->getPolicy(),
            'identifier' => $service->getCategory(),
        ]);

        if (!$category) {
            $this->logger->warning('Category not found for third-party service', [
                'service_id' => $service->getId(),
                'category_identifier' => $service->getCategory(),
            ]);
            return;
        }

        // Create cookies from preset
        foreach ($preset['cookies'] as $cookieData) {
            // Check if cookie already exists
            $existingCookie = $this->entityManager->getRepository(Cookie::class)->findOneBy([
                'category' => $category,
                'name' => $cookieData['name'],
            ]);

            if ($existingCookie) {
                $this->logger->info('Cookie already exists, skipping', [
                    'cookie_name' => $cookieData['name'],
                    'category' => $category->getIdentifier(),
                ]);
                continue;
            }

            $cookie = new Cookie();
            $cookie->setCategory($category);
            $cookie->setName($cookieData['name']);
            $cookie->setPurpose($cookieData['purpose']);
            $cookie->setExpiry($cookieData['expiry']);
            $cookie->setProvider($preset['name']);

            $this->entityManager->persist($cookie);

            $this->logger->info('Created cookie from preset', [
                'cookie_name' => $cookieData['name'],
                'service' => $service->getName(),
                'preset' => $presetType,
            ]);
        }

        $this->entityManager->flush();
    }
}
