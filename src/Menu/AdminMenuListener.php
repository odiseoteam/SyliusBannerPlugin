<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        /** @var ItemInterface $item */
        $item = $menu->getChild('catalog');
        if (null == $item) {
            $item = $menu;
        }

        $item
            ->addChild('banners', ['route' => 'odiseo_sylius_banner_plugin_admin_banner_index'])
            ->setLabel('odiseo_sylius_banner_plugin.ui.banners')
            ->setLabelAttribute('icon', 'image')
        ;
    }
}
