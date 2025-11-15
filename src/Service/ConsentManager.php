<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Service;

use Masilia\ConsentBundle\Entity\ConsentLog;
use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Event\ConsentChangedEvent;
use Masilia\ConsentBundle\Event\ConsentEvents;
use Masilia\ConsentBundle\Repository\ConsentLogRepository;
use Masilia\ConsentBundle\Repository\CookiePolicyRepository;
use Masilia\ConsentBundle\ValueObject\ConsentPreferences;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ConsentManager
{
    public function __construct(
        private readonly CookiePolicyRepository $policyRepository,
        private readonly ConsentLogRepository $logRepository,
        private readonly ConsentStorageHandler $storageHandler,
        private readonly RequestStack $requestStack,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly array $loggingConfig
    ) {
    }

    public function getActivePolicy(): ?CookiePolicy
    {
        return $this->policyRepository->findActivePolicy();
    }

    public function hasConsent(string $category): bool
    {
        $preferences = $this->storageHandler->getConsent();
        
        if (!$preferences) {
            return false;
        }

        return $preferences->hasConsent($category);
    }

    public function getConsentPreferences(): ?ConsentPreferences
    {
        return $this->storageHandler->getConsent();
    }

    public function acceptAll(Response $response): void
    {
        $policy = $this->getActivePolicy();
        if (!$policy) {
            throw new \RuntimeException('No active cookie policy found');
        }

        $categories = [];
        foreach ($policy->getCategories() as $category) {
            $categories[$category->getIdentifier()] = true;
        }

        $preferences = new ConsentPreferences($categories, $policy->getVersion());
        $this->saveConsent($preferences, $response);
    }

    public function rejectNonEssential(Response $response): void
    {
        $policy = $this->getActivePolicy();
        if (!$policy) {
            throw new \RuntimeException('No active cookie policy found');
        }

        $categories = [];
        foreach ($policy->getCategories() as $category) {
            $categories[$category->getIdentifier()] = $category->isRequired();
        }

        $preferences = new ConsentPreferences($categories, $policy->getVersion());
        $this->saveConsent($preferences, $response);
    }

    public function updatePreferences(array $categories, Response $response): void
    {
        $policy = $this->getActivePolicy();
        if (!$policy) {
            throw new \RuntimeException('No active cookie policy found');
        }

        // Ensure required categories are always enabled
        foreach ($policy->getCategories() as $category) {
            if ($category->isRequired()) {
                $categories[$category->getIdentifier()] = true;
            }
        }

        $preferences = new ConsentPreferences($categories, $policy->getVersion());
        $this->saveConsent($preferences, $response);
    }

    public function revokeConsent(Response $response): void
    {
        $oldPreferences = $this->storageHandler->getConsent();
        $this->storageHandler->clearConsent($response);

        if ($oldPreferences) {
            $event = new ConsentChangedEvent($oldPreferences, null);
            $this->eventDispatcher->dispatch($event, ConsentEvents::CONSENT_REVOKED);
        }
    }

    private function saveConsent(ConsentPreferences $preferences, Response $response): void
    {
        $oldPreferences = $this->storageHandler->getConsent();
        $this->storageHandler->saveConsent($preferences, $response);

        // Log consent if enabled
        if ($this->loggingConfig['enabled'] ?? true) {
            $this->logConsent($preferences);
        }

        // Dispatch event
        $event = new ConsentChangedEvent($oldPreferences, $preferences);
        $this->eventDispatcher->dispatch($event, ConsentEvents::CONSENT_CHANGED);
    }

    private function logConsent(ConsentPreferences $preferences): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        $log = new ConsentLog();
        $log->setSessionId($request->getSession()->getId());
        $log->setPolicyVersion($preferences->getVersion());
        $log->setPreferences($preferences->getCategories());

        if ($this->loggingConfig['log_ip_address'] ?? true) {
            $ipAddress = $request->getClientIp();
            if ($this->loggingConfig['anonymize_ip'] ?? false) {
                $ipAddress = $this->anonymizeIp($ipAddress);
            }
            $log->setIpAddress($ipAddress);
        }

        if ($this->loggingConfig['log_user_agent'] ?? true) {
            $log->setUserAgent($request->headers->get('User-Agent'));
        }

        $this->logRepository->save($log, true);
    }

    private function anonymizeIp(?string $ip): ?string
    {
        if (!$ip) {
            return null;
        }

        if (str_contains($ip, ':')) {
            // IPv6
            $parts = explode(':', $ip);
            $parts[count($parts) - 1] = '0';
            return implode(':', $parts);
        }

        // IPv4
        $parts = explode('.', $ip);
        $parts[3] = '0';
        return implode('.', $parts);
    }
}
