<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress UnusedVariable
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('odiseo_sylius_banner');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
