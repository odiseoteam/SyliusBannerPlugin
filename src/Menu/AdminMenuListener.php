<?php

namespace Odiseo\SyliusBannerPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event)
    {
        $menu = $event->getMenu();

        /** @var ItemInterface $item */
        $item = $menu->getChild('catalog');
        if (null == $item) {
            $item = $menu;
        }

        $item->addChild('banners', ['route' => 'odiseo_sylius_banner_admin_banner_index'])
            ->setLabel('odiseo_sylius_banner.ui.banners')
            ->setLabelAttribute('icon', 'image')
        ;
    }
}
