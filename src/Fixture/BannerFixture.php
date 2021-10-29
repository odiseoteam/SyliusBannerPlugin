<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class BannerFixture extends AbstractResourceFixture
{
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $node = $resourceNode->children();

        $node->arrayNode('channels')->scalarPrototype();
        $node->scalarNode('image');
        $node->scalarNode('mobile_image');
    }

    public function getName(): string
    {
        return 'banner';
    }
}
