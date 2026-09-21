<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class OdiseoSyliusBannerExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    private const IMAGE_DATA_ROOT = '%sylius_core.public_dir%/media/banner-image';

    /** @psalm-suppress UnusedVariable */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));

        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
        $this->prependLiipImagine($container);
    }

    private function prependLiipImagine(ContainerBuilder $container): void
    {
        /** @var array{images: array{format: string|null, quality: int, mobile_quality: int}} $config */
        $config = $this->processConfiguration(
            new Configuration(),
            $container->getExtensionConfig($this->getAlias()),
        );

        $images = $config['images'];

        $container->prependExtensionConfig('liip_imagine', [
            'loaders' => [
                'odiseo_banner_image' => [
                    'filesystem' => [
                        'data_root' => self::IMAGE_DATA_ROOT,
                    ],
                ],
            ],
            'filter_sets' => [
                'odiseo_banner_image' => $this->createFilterSet(
                    [1920, 600],
                    $images['quality'],
                    $images['format'],
                ),
                'odiseo_banner_mobile_image' => $this->createFilterSet(
                    [768, 1000],
                    $images['mobile_quality'],
                    $images['format'],
                ),
            ],
        ]);
    }

    /**
     * @param array{int, int} $size
     *
     * @return array<string, mixed>
     */
    private function createFilterSet(array $size, int $quality, ?string $format): array
    {
        $filterSet = [
            'data_loader' => 'odiseo_banner_image',
            'quality' => $quality,
            'filters' => [
                'thumbnail' => [
                    'size' => $size,
                    'mode' => 'inset',
                ],
            ],
        ];

        if (null !== $format) {
            $filterSet['format'] = $format;
        }

        return $filterSet;
    }

    protected function getMigrationsNamespace(): string
    {
        return 'Odiseo\\SyliusBannerPlugin\\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@OdiseoSyliusBannerPlugin/src/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }
}
