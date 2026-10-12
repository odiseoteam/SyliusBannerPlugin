<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Integration\DependencyInjection;

use Doctrine\Migrations\DependencyFactory;
use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\OdiseoSyliusBannerPlugin;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploaderInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ContainerTest extends KernelTestCase
{
    public function testThePluginIsRegisteredInTheKernel(): void
    {
        $kernel = self::bootKernel();

        self::assertInstanceOf(OdiseoSyliusBannerPlugin::class, $kernel->getBundle('OdiseoSyliusBannerPlugin'));
    }

    public function testItsMigrationsAreRegistered(): void
    {
        $dependencyFactory = self::getContainer()->get('doctrine.migrations.dependency_factory');
        self::assertInstanceOf(DependencyFactory::class, $dependencyFactory);

        self::assertArrayHasKey(
            'Odiseo\SyliusBannerPlugin\Migrations',
            $dependencyFactory->getConfiguration()->getMigrationDirectories(),
        );
    }

    public function testTheBannerIsASyliusResource(): void
    {
        self::assertSame(Banner::class, self::getContainer()->getParameter('odiseo_banner.model.banner.class'));
        self::assertInstanceOf(
            BannerRepositoryInterface::class,
            self::getContainer()->get('odiseo_banner.repository.banner'),
        );
    }

    public function testTheImagesAreStoredThroughFlysystem(): void
    {
        self::assertInstanceOf(
            FilesystemOperator::class,
            self::getContainer()->get('odiseo_banner.storage.banner_image'),
        );
        self::assertInstanceOf(
            BannerImageUploaderInterface::class,
            self::getContainer()->get('odiseo_banner.uploader.banner_image'),
        );
    }

    /** Both have to point at the same directory: one writes the images, the other reads them. */
    public function testTheImagesAreStoredWhereLiipImagineLoadsThemFrom(): void
    {
        $publicDir = self::getContainer()->getParameter('sylius_core.public_dir');
        self::assertIsString($publicDir);

        $loaders = self::getContainer()->getParameter('liip_imagine.loaders');
        self::assertIsArray($loaders);
        $loader = $loaders['odiseo_banner_image'] ?? null;
        self::assertIsArray($loader);
        $filesystem = $loader['filesystem'] ?? null;
        self::assertIsArray($filesystem);

        self::assertSame([$publicDir . '/media/banner-image'], $filesystem['data_root'] ?? null);
    }

    public function testTheFilterSetsOfTheImagesAreRegistered(): void
    {
        $filterSets = self::getContainer()->getParameter('liip_imagine.filter_sets');
        self::assertIsArray($filterSets);

        self::assertArrayHasKey('odiseo_banner_image', $filterSets);
        self::assertArrayHasKey('odiseo_banner_mobile_image', $filterSets);
    }
}
