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

        //Add new ones
        $this->addBannersSubMenu($menu->getChild('catalog'));
        $this->addBannerTypesSubMenu($menu->getChild('catalog'));
    }

    /**
     * @param ItemInterface $menu
     */
    private function addBannersSubMenu(ItemInterface $menu): void
    {
        $menu
            ->addChild('banners', ['route' => 'odiseo_sylius_banner_admin_banner_index'])
            ->setLabel('odiseo_sylius_banner.ui.banners')
            ->setLabelAttribute('icon', 'trademark')
        ;
    }

    /**
     * @param ItemInterface $menu
     */
    private function addBannerTypesSubMenu(ItemInterface $menu): void
    {
        $menu
            ->addChild('banner_types', ['route' => 'odiseo_sylius_banner_admin_banner_type_index'])
            ->setLabel('odiseo_sylius_banner.ui.banner_types')
            ->setLabelAttribute('icon', 'trademark')
        ;
    }
}
