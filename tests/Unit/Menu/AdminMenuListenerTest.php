<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use Odiseo\SyliusBannerPlugin\Menu\AdminMenuListener;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListenerTest extends TestCase
{
    public function testItAddsTheBannersUnderTheCatalog(): void
    {
        $factory = new MenuFactory();
        $menu = $factory->createItem('root');
        $catalog = $menu->addChild('catalog');

        (new AdminMenuListener())->addAdminMenuItems(new MenuBuilderEvent($factory, $menu));

        $banners = $catalog->getChild('banners');
        self::assertInstanceOf(ItemInterface::class, $banners);
        self::assertSame('odiseo_banner.menu.admin.banners', $banners->getLabel());
        self::assertSame('image', $banners->getLabelAttribute('icon'));
        self::assertNull($menu->getChild('banners'));
    }

    public function testItFallsBackToTheRootWhenThereIsNoCatalog(): void
    {
        $factory = new MenuFactory();
        $menu = $factory->createItem('root');

        (new AdminMenuListener())->addAdminMenuItems(new MenuBuilderEvent($factory, $menu));

        self::assertInstanceOf(ItemInterface::class, $menu->getChild('banners'));
    }
}
