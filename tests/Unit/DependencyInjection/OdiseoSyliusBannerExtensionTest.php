<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\DependencyInjection;

use Odiseo\SyliusBannerPlugin\DependencyInjection\OdiseoSyliusBannerExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OdiseoSyliusBannerExtensionTest extends TestCase
{
    public function testItPrependsTheFilterSetsWithTheDefaultFormatAndQuality(): void
    {
        $filterSets = $this->prependedFilterSets();

        self::assertSame('webp', $filterSets['odiseo_banner_image']['format']);
        self::assertSame(80, $filterSets['odiseo_banner_image']['quality']);
        self::assertSame('webp', $filterSets['odiseo_banner_mobile_image']['format']);
        self::assertSame(75, $filterSets['odiseo_banner_mobile_image']['quality']);
    }

    public function testItAppliesTheConfiguredFormatAndQuality(): void
    {
        $filterSets = $this->prependedFilterSets([
            'images' => ['format' => 'jpeg', 'quality' => 60, 'mobile_quality' => 50],
        ]);

        self::assertSame('jpeg', $filterSets['odiseo_banner_image']['format']);
        self::assertSame(60, $filterSets['odiseo_banner_image']['quality']);
        self::assertSame('jpeg', $filterSets['odiseo_banner_mobile_image']['format']);
        self::assertSame(50, $filterSets['odiseo_banner_mobile_image']['quality']);
    }

    public function testItKeepsTheOriginalFormatWhenTheFormatIsNull(): void
    {
        $filterSets = $this->prependedFilterSets(['images' => ['format' => null]]);

        self::assertArrayNotHasKey('format', $filterSets['odiseo_banner_image']);
        self::assertArrayNotHasKey('format', $filterSets['odiseo_banner_mobile_image']);
    }

    public function testItKeepsTheThumbnailSizes(): void
    {
        $filterSets = $this->prependedFilterSets();

        self::assertSame(
            ['size' => [1920, 600], 'mode' => 'inset'],
            $filterSets['odiseo_banner_image']['filters']['thumbnail'],
        );
        self::assertSame(
            ['size' => [768, 1000], 'mode' => 'inset'],
            $filterSets['odiseo_banner_mobile_image']['filters']['thumbnail'],
        );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, array<string, mixed>>
     */
    private function prependedFilterSets(array $config = []): array
    {
        $extension = new OdiseoSyliusBannerExtension();

        $container = new ContainerBuilder();
        $container->registerExtension($extension);

        if ([] !== $config) {
            $container->loadFromExtension('odiseo_sylius_banner', $config);
        }

        $extension->prepend($container);

        return $container->getExtensionConfig('liip_imagine')[0]['filter_sets'];
    }
}
