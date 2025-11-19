<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Service;

use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;

class SiteAccessProvider
{
    public function __construct(
        private readonly SiteAccessServiceInterface $siteAccessService
    ) {
    }

    /**
     * Get all available siteaccesses as choices for form dropdown
     * 
     * @return array<string, string> Array with siteaccess name as both key and value
     */
    public function getSiteAccessChoices(): array
    {
        $siteAccesses = $this->siteAccessService->getAll();
        $choices = [];

        foreach ($siteAccesses as $siteAccess) {
            $name = $siteAccess->name;
            $choices[$name] = $name;
        }

        // Sort alphabetically
        ksort($choices);

        return $choices;
    }

    /**
     * Get current siteaccess name
     */
    public function getCurrentSiteAccess(): string
    {
        return $this->siteAccessService->getCurrent()->name;
    }
}
