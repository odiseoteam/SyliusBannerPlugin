<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class BannerFixture extends AbstractResourceFixture
{
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $node = $resourceNode->children();

        $node->scalarNode('code')->cannotBeEmpty();
        $node->arrayNode('channels')->scalarPrototype();
        $node->arrayNode('taxons')->scalarPrototype();
        $node->scalarNode('url');
        $node->scalarNode('main_text');
        $node->scalarNode('secondary_text');
        $node->scalarNode('button_text');
        $node->scalarNode('image');
        $node->scalarNode('mobile_image');
    }

    public function getName(): string
    {
        return 'banner';
    }
}
