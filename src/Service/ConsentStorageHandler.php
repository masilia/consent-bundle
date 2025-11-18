<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Service;

use Masilia\ConsentBundle\ValueObject\ConsentPreferences;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ConsentStorageHandler
{
    private const COOKIE_NAME = 'masilia_consent';
    private array $storageConfig;

    public function __construct(
        private readonly RequestStack $requestStack,
        public readonly ParameterBagInterface $parameterBag
    ) {
        $this->storageConfig = $this->parameterBag->get('masilia_consent.storage');
    }

    public function getConsent(): ?ConsentPreferences
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return null;
        }

        $cookieValue = $request->cookies->get($this->getCookieName());
        if (!$cookieValue) {
            return null;
        }

        try {
            $data = json_decode($cookieValue, true, 512, JSON_THROW_ON_ERROR);
            return ConsentPreferences::fromArray($data);
        } catch (\JsonException) {
            return null;
        }
    }

    public function saveConsent(ConsentPreferences $preferences, Response $response): void
    {
        $cookieValue = json_encode($preferences->toArray());
        
        $cookie = Cookie::create(
            $this->getCookieName(),
            $cookieValue,
            $this->getCookieExpiration(),
            $this->storageConfig['cookie_path'] ?? '/',
            $this->storageConfig['cookie_domain'] ?? null,
            $this->storageConfig['cookie_secure'] ?? true,
            $this->storageConfig['cookie_http_only'] ?? true,
            false,
            $this->storageConfig['cookie_same_site'] ?? Cookie::SAMESITE_LAX
        );

        $response->headers->setCookie($cookie);
    }

    public function clearConsent(Response $response): void
    {
        $cookie = Cookie::create(
            $this->getCookieName(),
            '',
            1, // Expired
            $this->storageConfig['cookie_path'] ?? '/',
            $this->storageConfig['cookie_domain'] ?? null
        );

        $response->headers->setCookie($cookie);
    }

    public function hasConsent(): bool
    {
        return $this->getConsent() !== null;
    }

    private function getCookieName(): string
    {
        return $this->storageConfig['cookie_name'] ?? self::COOKIE_NAME;
    }

    private function getCookieExpiration(): int
    {
        $lifetime = $this->storageConfig['cookie_lifetime'] ?? 365;
        return time() + ($lifetime * 24 * 60 * 60);
    }
}
