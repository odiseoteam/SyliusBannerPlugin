<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('odiseo_sylius_banner_plugin');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('odiseo_sylius_banner_plugin');
        }

        $rootNode
            ->children()
                ->scalarNode('slider')->defaultValue('swiper')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
