<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Service;

use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Repository\CookieRepository;
use Masilia\ConsentBundle\Repository\ThirdPartyServiceRepository;

readonly class ScriptInjectionService
{
    public function __construct(
        private CookieRepository $cookieRepository,
        private ConsentManager $consentManager,
        private ThirdPartyServiceRepository $serviceRepository,
        private CookiePresetService $presetService
    ) {
    }

    public function getScriptsForCategory(CookieCategory $category): array
    {
        if (!$this->consentManager->hasConsent($category->getIdentifier())) {
            return [];
        }

        $cookies = $this->cookieRepository->findWithScripts($category);
        $scripts = [];

        foreach ($cookies as $cookie) {
            if ($cookie->getScriptSrc()) {
                $scripts[] = [
                    'type' => 'external',
                    'src' => $cookie->getScriptSrc(),
                    'async' => $cookie->isScriptAsync(),
                    'name' => $cookie->getName(),
                ];
            }

            if ($cookie->getInitCode()) {
                $scripts[] = [
                    'type' => 'inline',
                    'code' => $cookie->getInitCode(),
                    'name' => $cookie->getName(),
                ];
            }
        }

        return $scripts;
    }

    public function generateScriptTags(CookieCategory $category): string
    {
        $scripts = $this->getScriptsForCategory($category);
        $html = '';

        foreach ($scripts as $script) {
            if ($script['type'] === 'external') {
                $async = $script['async'] ? ' async' : '';
                $html .= sprintf(
                    '<script src="%s"%s data-consent-category="%s"></script>' . PHP_EOL,
                    htmlspecialchars($script['src'], ENT_QUOTES, 'UTF-8'),
                    $async,
                    htmlspecialchars($category->getIdentifier(), ENT_QUOTES, 'UTF-8')
                );
            } elseif ($script['type'] === 'inline') {
                $html .= sprintf(
                    '<script data-consent-category="%s">%s</script>' . PHP_EOL,
                    htmlspecialchars($category->getIdentifier(), ENT_QUOTES, 'UTF-8'),
                    $script['code']
                );
            }
        }

        return $html;
    }

    public function shouldInjectScripts(string $categoryIdentifier): bool
    {
        return $this->consentManager->hasConsent($categoryIdentifier);
    }

    /**
     * Get scripts from third-party services for a category
     */
    public function getServiceScriptsForCategory(CookieCategory $category): string
    {
        if (!$this->consentManager->hasConsent($category->getIdentifier())) {
            return '';
        }

        $services = $this->serviceRepository->findBy([
            'policy' => $category->getPolicy(),
            'category' => $category->getIdentifier(),
            'enabled' => true,
        ]);

        $html = '';
        foreach ($services as $service) {
            if ($service->getPresetType()) {
                $script = $this->presetService->getScriptForPreset(
                    $service->getPresetType(),
                    $service->getConfigValue()
                );
                
                if ($script) {
                    $html .= $script . PHP_EOL;
                }
            }
        }

        return $html;
    }

    /**
     * Generate all scripts (cookies + services) for a category
     */
    public function generateAllScripts(CookieCategory $category): string
    {
        $html = '';
        
        // Add cookie scripts
        $html .= $this->generateScriptTags($category);
        
        // Add service scripts
        $html .= $this->getServiceScriptsForCategory($category);
        
        return $html;
    }
}
