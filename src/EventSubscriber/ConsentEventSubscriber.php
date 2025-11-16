<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\EventSubscriber;

use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class ConsentEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private bool $loggingEnabled = true
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsentChangedEvent::class => [
                ['onConsentChanged', 10],
                ['logConsentChange', 0],
            ],
        ];
    }

    /**
     * Main handler for consent changes
     */
    public function onConsentChanged(ConsentChangedEvent $event): void
    {
        $old = $event->getOldPreferences();
        $new = $event->getNewPreferences();

        // Handle initial consent (no previous preferences)
        if (!$old && $new) {
            $this->handleInitialConsent($event);
            return;
        }

        // Handle consent revocation (had preferences, now null)
        if ($old && !$new) {
            $this->handleConsentRevocation($event);
            return;
        }

        // Handle consent update (both exist)
        if ($old && $new) {
            $this->handleConsentUpdate($event);
        }
    }

    /**
     * Log consent changes for audit trail
     */
    public function logConsentChange(ConsentChangedEvent $event): void
    {
        if (!$this->loggingEnabled) {
            return;
        }

        $old = $event->getOldPreferences();
        $new = $event->getNewPreferences();

        $context = [
            'old_policy_version' => $old?->getVersion(),
            'new_policy_version' => $new?->getVersion(),
            'old_categories' => $old?->getCategories() ?? [],
            'new_categories' => $new?->getCategories() ?? [],
            'timestamp' => $new?->getTimestamp() ?? new \DateTime(),
        ];

        if (!$old && $new) {
            $this->logger->info('User gave initial consent', $context);
        } elseif ($old && !$new) {
            $this->logger->info('User revoked all consent', $context);
        } elseif ($old && $new) {
            $changes = $this->getConsentChanges($old->getCategories(), $new->getCategories());
            if (!empty($changes)) {
                $context['changes'] = $changes;
                $this->logger->info('User updated consent preferences', $context);
            }
        }
    }

    /**
     * Handle initial consent given by user
     */
    private function handleInitialConsent(ConsentChangedEvent $event): void
    {
        $preferences = $event->getNewPreferences();
        if (!$preferences) {
            return;
        }

        $acceptedCategories = array_filter(
            $preferences->getCategories(),
            static fn($consented) => $consented === true
        );

        $this->logger->debug('Initial consent given', [
            'policy_version' => $preferences->getVersion(),
            'accepted_categories' => array_keys($acceptedCategories),
            'total_categories' => count($preferences->getCategories()),
        ]);

        // Here you could trigger additional actions:
        // - Send analytics event
        // - Update user profile
        // - Enable features based on consent
    }

    /**
     * Handle complete consent revocation
     */
    private function handleConsentRevocation(ConsentChangedEvent $event): void
    {
        $oldPreferences = $event->getOldPreferences();
        if (!$oldPreferences) {
            return;
        }

        $this->logger->warning('User revoked all consent', [
            'previous_policy_version' => $oldPreferences->getVersion(),
            'revoked_categories' => array_keys($oldPreferences->getCategories()),
        ]);

        // Here you could trigger additional actions:
        // - Clear user tracking data
        // - Disable analytics
        // - Remove personalization
    }

    /**
     * Handle consent preference updates
     */
    private function handleConsentUpdate(ConsentChangedEvent $event): void
    {
        $old = $event->getOldPreferences();
        $new = $event->getNewPreferences();

        if (!$old || !$new) {
            return;
        }

        $changes = $this->getConsentChanges($old->getCategories(), $new->getCategories());

        foreach ($changes['granted'] as $category) {
            $this->logger->debug("Consent granted for category: {$category}");
            // Trigger category-specific actions
        }

        foreach ($changes['revoked'] as $category) {
            $this->logger->debug("Consent revoked for category: {$category}");
            // Trigger category-specific cleanup
        }

        // Check for policy version change
        if ($old->getVersion() !== $new->getVersion()) {
            $this->logger->info('User accepted new policy version', [
                'old_version' => $old->getVersion(),
                'new_version' => $new->getVersion(),
            ]);
        }
    }

    /**
     * Get detailed changes between old and new consent
     */
    private function getConsentChanges(array $oldCategories, array $newCategories): array
    {
        $granted = [];
        $revoked = [];

        foreach ($newCategories as $category => $consented) {
            $oldConsent = $oldCategories[$category] ?? false;
            
            if ($consented && !$oldConsent) {
                $granted[] = $category;
            } elseif (!$consented && $oldConsent) {
                $revoked[] = $category;
            }
        }

        return [
            'granted' => $granted,
            'revoked' => $revoked,
        ];
    }
}
