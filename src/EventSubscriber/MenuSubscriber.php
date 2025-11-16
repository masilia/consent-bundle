<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\EventSubscriber;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onMenuConfigure', 0],
        ];
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        // Add main "Cookie Consent" menu item with direct link to policies
        $menu->addChild(
            'masilia_consent',
            [
                'label' => 'menu.consent',
                'route' => 'masilia_consent_admin_policy_list',
                'extras' => [
                    'icon' => 'shield-check',
                    'translation_domain' => 'masilia_consent',
                    'orderNumber' => 100,
                ],
            ]
        );
    }
}
