<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Service;

use Ibexa\Contracts\Core\Repository\LanguageService;
use Masilia\ConsentBundle\Entity\Cookie;
use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Entity\ThirdPartyService;

/**
 * Service to resolve translations for consent entities based on current siteaccess language
 */
readonly class TranslationResolver
{
    public function __construct(
        private LanguageService $languageService
    ) {
    }

    /**
     * Get current language code from Ibexa's language service
     * This automatically returns the default language for the current siteaccess
     */
    public function getCurrentLanguage(): string
    {
        return $this->languageService->getDefaultLanguageCode();
    }

    /**
     * Get fallback language code (first enabled language in the system)
     */
    private function getFallbackLanguage(): string
    {
        $languages = $this->languageService->loadLanguages();
        
        // Return first enabled language as fallback
        foreach ($languages as $language) {
            if ($language->isEnabled()) {
                return $language->languageCode;
            }
        }
        
        // Ultimate fallback if no languages enabled
        return 'eng-GB';
    }

    /**
     * Get translated name for cookie category
     */
    public function getCategoryName(CookieCategory $category, ?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguage();
        
        // Try to get translation for requested language
        $translation = $category->getTranslation($languageCode);
        if ($translation) {
            return $translation->getName();
        }
        
        // Fallback to system's fallback language
        $fallbackLanguage = $this->getFallbackLanguage();
        if ($languageCode !== $fallbackLanguage) {
            $fallbackTranslation = $category->getTranslation($fallbackLanguage);
            if ($fallbackTranslation) {
                return $fallbackTranslation->getName();
            }
        }
        
        // Last resort: use base entity name
        return $category->getName();
    }

    /**
     * Get translated description for cookie category
     */
    public function getCategoryDescription(CookieCategory $category, ?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguage();
        
        // Try to get translation for requested language
        $translation = $category->getTranslation($languageCode);
        if ($translation && $translation->getDescription()) {
            return $translation->getDescription();
        }
        
        // Fallback to system's fallback language
        $fallbackLanguage = $this->getFallbackLanguage();
        if ($languageCode !== $fallbackLanguage) {
            $fallbackTranslation = $category->getTranslation($fallbackLanguage);
            if ($fallbackTranslation && $fallbackTranslation->getDescription()) {
                return $fallbackTranslation->getDescription();
            }
        }
        
        // Last resort: use base entity description
        return $category->getDescription();
    }

    /**
     * Get translated name for cookie
     */
    public function getCookieName(Cookie $cookie, ?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguage();
        
        // Try to get translation for requested language
        $translation = $cookie->getTranslation($languageCode);
        if ($translation) {
            return $translation->getName();
        }
        
        // Fallback to system's fallback language
        $fallbackLanguage = $this->getFallbackLanguage();
        if ($languageCode !== $fallbackLanguage) {
            $fallbackTranslation = $cookie->getTranslation($fallbackLanguage);
            if ($fallbackTranslation) {
                return $fallbackTranslation->getName();
            }
        }
        
        // Last resort: use base entity name
        return $cookie->getName();
    }

    /**
     * Get translated description for cookie
     */
    public function getCookieDescription(Cookie $cookie, ?string $languageCode = null): ?string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguage();
        
        // Try to get translation for requested language
        $translation = $cookie->getTranslation($languageCode);
        if ($translation && $translation->getDescription()) {
            return $translation->getDescription();
        }
        
        // Fallback to system's fallback language
        $fallbackLanguage = $this->getFallbackLanguage();
        if ($languageCode !== $fallbackLanguage) {
            $fallbackTranslation = $cookie->getTranslation($fallbackLanguage);
            if ($fallbackTranslation && $fallbackTranslation->getDescription()) {
                return $fallbackTranslation->getDescription();
            }
        }
        
        return null;
    }

    /**
     * Get translated purpose for cookie
     */
    public function getCookiePurpose(Cookie $cookie, ?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguage();
        
        // Try to get translation for requested language
        $translation = $cookie->getTranslation($languageCode);
        if ($translation && $translation->getPurpose()) {
            return $translation->getPurpose();
        }
        
        // Fallback to system's fallback language
        $fallbackLanguage = $this->getFallbackLanguage();
        if ($languageCode !== $fallbackLanguage) {
            $fallbackTranslation = $cookie->getTranslation($fallbackLanguage);
            if ($fallbackTranslation && $fallbackTranslation->getPurpose()) {
                return $fallbackTranslation->getPurpose();
            }
        }
        
        // Last resort: use base entity purpose
        return $cookie->getPurpose();
    }

    /**
     * Get translated name for third-party service
     */
    public function getServiceName(ThirdPartyService $service, ?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguage();
        
        // Try to get translation for requested language
        $translation = $service->getTranslation($languageCode);
        if ($translation) {
            return $translation->getName();
        }
        
        // Fallback to system's fallback language
        $fallbackLanguage = $this->getFallbackLanguage();
        if ($languageCode !== $fallbackLanguage) {
            $fallbackTranslation = $service->getTranslation($fallbackLanguage);
            if ($fallbackTranslation) {
                return $fallbackTranslation->getName();
            }
        }
        
        // Last resort: use base entity name
        return $service->getName();
    }

    /**
     * Get translated description for third-party service
     */
    public function getServiceDescription(ThirdPartyService $service, ?string $languageCode = null): string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguage();
        
        // Try to get translation for requested language
        $translation = $service->getTranslation($languageCode);
        if ($translation && $translation->getDescription()) {
            return $translation->getDescription();
        }
        
        // Fallback to system's fallback language
        $fallbackLanguage = $this->getFallbackLanguage();
        if ($languageCode !== $fallbackLanguage) {
            $fallbackTranslation = $service->getTranslation($fallbackLanguage);
            if ($fallbackTranslation && $fallbackTranslation->getDescription()) {
                return $fallbackTranslation->getDescription();
            }
        }
        
        // Last resort: use base entity description
        return $service->getDescription();
    }

    /**
     * Get all available languages for translation from Ibexa
     * 
     * @return array<string, string> Language code => Language name
     */
    public function getAvailableLanguages(): array
    {
        $languages = $this->languageService->loadLanguages();
        $result = [];
        
        foreach ($languages as $language) {
            if ($language->enabled) {
                $result[$language->languageCode] = $language->name;
            }
        }
        
        return $result;
    }

    /**
     * Check if a language is RTL (Right-to-Left)
     */
    public function isRTL(string $languageCode): bool
    {
        return str_starts_with($languageCode, 'ar-');
    }
}
