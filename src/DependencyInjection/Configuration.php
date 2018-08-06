<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\DependencyInjection;

use Odiseo\SyliusBannerPlugin\Doctrine\ORM\BannerRepository;
use Odiseo\SyliusBannerPlugin\Form\Type\BannerTranslationType;
use Odiseo\SyliusBannerPlugin\Form\Type\BannerType;
use Odiseo\SyliusBannerPlugin\Model\Banner;
use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use Odiseo\SyliusBannerPlugin\Model\BannerTranslation;
use Odiseo\SyliusBannerPlugin\Model\BannerTranslationInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('odiseo_sylius_banner');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('banner')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Banner::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(BannerInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(BannerRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(BannerType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(BannerTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(BannerTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->defaultValue(EntityRepository::class)->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->arrayNode('form')
                                                    ->addDefaultsIfNotSet()
                                                    ->children()
                                                        ->scalarNode('default')->defaultValue(BannerTranslationType::class)->cannotBeEmpty()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
