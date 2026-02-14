<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

readonly class CategoryMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory
    ) {
    }

    /**
     * Create menu for category create/edit sidebar
     */
    public function createCategorySidebarMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $saveId = $options['save_id'] ?? null;
        $cancelUrl = $options['cancel_url'] ?? null;

        if ($saveId) {
            $menu->addChild('save', [
                'label' => 'category.form.save',
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
