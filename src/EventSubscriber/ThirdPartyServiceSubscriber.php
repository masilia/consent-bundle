<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Masilia\ConsentBundle\Entity\Cookie;
use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Masilia\ConsentBundle\Service\CookiePresetService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Automatically manages category and cookies when a third-party service with a preset is created, updated, or deleted
 */
readonly class ThirdPartyServiceSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CookiePresetService $presetService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
        ];
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

        $this->createCategoryFromPreset($entity);
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof ThirdPartyService) {
            return;
        }

        // If category exists, update it; otherwise create if preset is set
        if ($entity->getCookieCategory()) {
            if ($entity->getPresetType()) {
                $this->updateCategoryFromPreset($entity);
            } else {
                // Remove category if preset was removed
                $this->entityManager->remove($entity->getCookieCategory());
                $entity->setCookieCategory(null);
                $this->entityManager->flush();
            }
        } elseif ($entity->getPresetType()) {
            $this->createCategoryFromPreset($entity);
        }
    }

    private function createCategoryFromPreset(ThirdPartyService $service): void
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

        // Create category for this service
        $category = new CookieCategory();
        $category->setPolicy($service->getPolicy());
        $category->setIdentifier($service->getIdentifier() . '_category');
        $category->setName($service->getName());
        $category->setDescription($preset['description']);
        $category->setRequired(false);
        $category->setDefaultEnabled(false);
        $category->setPosition(999); // Put at end by default

        // Create cookies from preset
        foreach ($preset['cookies'] as $cookieData) {
            $cookie = new Cookie();
            $cookie->setName($cookieData['name']);
            $cookie->setPurpose($cookieData['purpose']);
            $cookie->setExpiry($cookieData['expiry']);
            $cookie->setProvider($preset['name']);
            $cookie->setPosition(0);

            $category->addCookie($cookie);

            $this->logger->info('Created cookie from preset', [
                'cookie_name' => $cookieData['name'],
                'service' => $service->getName(),
                'preset' => $presetType,
            ]);
        }

        $service->setCookieCategory($category);
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->logger->info('Created category from service preset', [
            'service' => $service->getName(),
            'category' => $category->getName(),
            'preset' => $presetType,
            'cookies_count' => count($preset['cookies']),
        ]);
    }

    private function updateCategoryFromPreset(ThirdPartyService $service): void
    {
        $category = $service->getCookieCategory();
        if (!$category) {
            return;
        }

        $presetType = $service->getPresetType();
        $preset = $this->presetService->getPreset($presetType);

        if (!$preset) {
            $this->logger->warning('Preset not found for third-party service update', [
                'service_id' => $service->getId(),
                'preset_type' => $presetType,
            ]);
            return;
        }

        // Update category info
        $category->setName($service->getName());
        $category->setDescription($preset['description']);

        // Remove all existing cookies
        foreach ($category->getCookies()->toArray() as $cookie) {
            $category->removeCookie($cookie);
            $this->entityManager->remove($cookie);
        }

        // Create new cookies from preset
        foreach ($preset['cookies'] as $cookieData) {
            $cookie = new Cookie();
            $cookie->setName($cookieData['name']);
            $cookie->setPurpose($cookieData['purpose']);
            $cookie->setExpiry($cookieData['expiry']);
            $cookie->setProvider($preset['name']);
            $cookie->setPosition(0);

            $category->addCookie($cookie);

            $this->logger->info('Updated cookie from preset', [
                'cookie_name' => $cookieData['name'],
                'service' => $service->getName(),
                'preset' => $presetType,
            ]);
        }

        $this->entityManager->flush();

        $this->logger->info('Updated category from service preset', [
            'service' => $service->getName(),
            'category' => $category->getName(),
            'preset' => $presetType,
            'cookies_count' => count($preset['cookies']),
        ]);
    }
}
