<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Service;

use Masilia\ConsentBundle\Entity\CookieCategory;
use Masilia\ConsentBundle\Repository\CookieRepository;

readonly class ScriptInjectionService
{
    public function __construct(
        private CookieRepository $cookieRepository,
        private ConsentManager $consentManager
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
}
