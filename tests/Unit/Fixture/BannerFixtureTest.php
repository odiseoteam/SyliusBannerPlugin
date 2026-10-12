<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Fixture;

use Doctrine\Persistence\ObjectManager;
use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Fixture\BannerFixture;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

final class BannerFixtureTest extends TestCase
{
    public function testItIsTheBannerFixture(): void
    {
        self::assertSame('banner', $this->fixture()->getName());
    }

    public function testItAcceptsEveryBannerOption(): void
    {
        $banner = [
            'code' => 'summer_sale',
            'position' => 2,
            'channels' => ['WEB_US'],
            'taxons' => ['t_shirts'],
            'url' => 'https://example.com/sale',
            'main_text' => 'Summer sale',
            'secondary_text' => 'Up to 50% off',
            'button_text' => 'Shop now',
            'image' => 'banner.png',
            'mobile_image' => 'banner-mobile.png',
        ];

        $options = (new Processor())->processConfiguration($this->fixture(), [['custom' => [$banner]]]);

        self::assertSame([$banner], $options['custom']);
    }

    public function testItRejectsAnEmptyCode(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration($this->fixture(), [['custom' => [['code' => '']]]]);
    }

    public function testItPersistsTheBannersItsFactoryCreates(): void
    {
        $banner = new Banner();

        $factory = $this->createMock(ExampleFactoryInterface::class);
        $factory->expects(self::once())->method('create')->with(['code' => 'summer_sale'])->willReturn($banner);

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('persist')->with($banner);
        $manager->expects(self::once())->method('flush');

        (new BannerFixture($manager, $factory))->load(['custom' => [['code' => 'summer_sale']]]);
    }

    private function fixture(): BannerFixture
    {
        return new BannerFixture(
            $this->createMock(ObjectManager::class),
            $this->createMock(ExampleFactoryInterface::class),
        );
    }
}
