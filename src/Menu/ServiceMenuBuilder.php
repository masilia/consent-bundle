<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

readonly class ServiceMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory
    ) {
    }

    /**
     * Create menu for third-party service create/edit sidebar
     */
    public function createServiceSidebarMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $saveId = $options['save_id'] ?? null;
        $cancelUrl = $options['cancel_url'] ?? null;

        if ($saveId) {
            $menu->addChild('save', [
                'label' => 'third_party_service.form.save',
                'translation_domain' => 'masilia_consent',
                'extras' => [
                    'icon' => 'checkmark',
                    'orderNumber' => 10,
                ],
                'attributes' => [
                    'class' => 'ibexa-btn--trigger',
                    'data-click' => sprintf('#%s', $saveId),
                ],
            ]);
        }

        if ($cancelUrl) {
            $menu->addChild('cancel', [
                'label' => 'common.cancel',
                'translation_domain' => 'masilia_consent',
                'uri' => $cancelUrl,
                'extras' => [
                    'icon' => 'circle-close',
                    'orderNumber' => 20,
                ],
                'attributes' => [
                    'class' => 'ibexa-btn--secondary',
                ],
            ]);
        }

        return $menu;
    }
}
