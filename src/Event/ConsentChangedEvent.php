<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Event;

use Masilia\ConsentBundle\ValueObject\ConsentPreferences;
use Symfony\Contracts\EventDispatcher\Event;

class ConsentChangedEvent extends Event
{
    public function __construct(
        private readonly ?ConsentPreferences $oldPreferences,
        private readonly ?ConsentPreferences $newPreferences
    ) {
    }

    public function getOldPreferences(): ?ConsentPreferences
    {
        return $this->oldPreferences;
    }

    public function getNewPreferences(): ?ConsentPreferences
    {
        return $this->newPreferences;
    }

    public function hasConsentChanged(string $category): bool
    {
        $oldConsent = $this->oldPreferences?->hasConsent($category) ?? false;
        $newConsent = $this->newPreferences?->hasConsent($category) ?? false;

        return $oldConsent !== $newConsent;
    }

    public function isConsentGranted(string $category): bool
    {
        return $this->newPreferences?->hasConsent($category) ?? false;
    }

    public function isConsentRevoked(string $category): bool
    {
        $oldConsent = $this->oldPreferences?->hasConsent($category) ?? false;
        $newConsent = $this->newPreferences?->hasConsent($category) ?? false;

        return $oldConsent && !$newConsent;
    }
}
