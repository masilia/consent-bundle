<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Twig;

use Masilia\ConsentBundle\Repository\CookiePolicyRepository;
use Masilia\ConsentBundle\Service\ConsentStorageHandler;
use Masilia\ConsentBundle\Service\ScriptInjectionService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ConsentExtension extends AbstractExtension
{
    public function __construct(
        private readonly ConsentStorageHandler $storageHandler,
        private readonly CookiePolicyRepository $policyRepository,
        private readonly ScriptInjectionService $scriptInjectionService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('consent_check', [$this, 'checkConsent']),
            new TwigFunction('consent_has', [$this, 'hasConsent']),
            new TwigFunction('consent_scripts', [$this, 'getScripts'], ['is_safe' => ['html']]),
            new TwigFunction('consent_banner', [$this, 'renderBanner'], ['is_safe' => ['html']]),
            new TwigFunction('consent_policy', [$this, 'getActivePolicy']),
            new TwigFunction('consent_categories', [$this, 'getCategories']),
            new TwigFunction('consent_preferences', [$this, 'getPreferences']),
        ];
    }

    /**
     * Check if user has consented to a specific category
     */
    public function checkConsent(string $categoryIdentifier): bool
    {
        $preferences = $this->storageHandler->getConsent();
        
        if (!$preferences) {
            return false;
        }

        return $preferences->hasConsent($categoryIdentifier);
    }

    /**
     * Alias for checkConsent (more natural in templates)
     */
    public function hasConsent(string $categoryIdentifier): bool
    {
        return $this->checkConsent($categoryIdentifier);
    }

    /**
     * Get and inject scripts for a specific category if consent is given
     */
    public function getScripts(string $categoryIdentifier): string
    {
        if (!$this->checkConsent($categoryIdentifier)) {
            return '';
        }

        $policy = $this->policyRepository->findActivePolicy();
        if (!$policy) {
            return '';
        }

        $category = null;
        foreach ($policy->getCategories() as $cat) {
            if ($cat->getIdentifier() === $categoryIdentifier) {
                $category = $cat;
                break;
            }
        }

        if (!$category) {
            return '';
        }

        return $this->scriptInjectionService->generateScriptTags($category);
    }

    /**
     * Render the consent banner HTML
     */
    public function renderBanner(): string
    {
        $policy = $this->policyRepository->findActivePolicy();
        if (!$policy) {
            return '';
        }

        $preferences = $this->storageHandler->getConsent();
        
        // Don't show banner if user already has preferences for current policy version
        if ($preferences && $preferences->getVersion() === $policy->getVersion()) {
            return '';
        }

        // Return placeholder div that React will mount to
        return '<div id="masilia-consent-banner" data-policy-version="' . htmlspecialchars($policy->getVersion()) . '"></div>';
    }

    /**
     * Get the active cookie policy
     */
    public function getActivePolicy(): ?object
    {
        return $this->policyRepository->findActivePolicy();
    }

    /**
     * Get all categories from active policy
     */
    public function getCategories(): array
    {
        $policy = $this->policyRepository->findActivePolicy();
        if (!$policy) {
            return [];
        }

        return $policy->getCategories()->toArray();
    }

    /**
     * Get user's current consent preferences
     */
    public function getPreferences(): ?array
    {
        $preferences = $this->storageHandler->getConsent();
        if (!$preferences) {
            return null;
        }

        return [
            'policy_version' => $preferences->getVersion(),
            'categories' => $preferences->getCategories(),
            'timestamp' => $preferences->getTimestamp(),
        ];
    }
}
