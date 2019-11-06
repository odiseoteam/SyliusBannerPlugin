<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Block;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

final class BannerCssBlockListener
{
    /**
     * @param BlockEvent $event
     */
    public function onBlockEvent(BlockEvent $event): void
    {
        $template = '@OdiseoSyliusBannerPlugin/Shop/Banner/_banner_css.html.twig';

        $block = new Block();
        $block->setId(uniqid('', true));
        $block->setSettings(array_replace($event->getSettings(), [
            'template' => $template,
        ]));
        $block->setType('sonata.block.service.template');

        $event->addBlock($block);
    }
}
