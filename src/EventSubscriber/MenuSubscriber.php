<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\EventSubscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => 'onMainMenuBuild',
        ];
    }

    public function onMainMenuBuild(ConfigureMenuEvent $event): void
    {
        $this->addCookieConsentMenu($event->getMenu());
    }

    /**
     * Adds the Cookie Consent menu to Ibexa admin interface.
     */
    private function addCookieConsentMenu(ItemInterface $menu): void
    {
        $menu
            ->addChild(
                'masilia_consent',
                [
                    'route' => 'masilia_consent_admin_policy_list',
                ]
            )
            ->setLabel('menu.consent')
            ->setExtra('translation_domain', 'masilia_consent')
            ->setExtra('icon', 'content-type-group')
            ->setExtra('orderNumber', 100)
            ->setAttribute('data-tooltip-placement', 'right')
            ->setAttribute('data-tooltip-extra-class', 'ibexa-tooltip--info-neon');
    }
}
