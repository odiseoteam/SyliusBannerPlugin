<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Block;

use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

final class SliderJsBlockListener
{
    /** @var string|null */
    private $slider;

    public function __construct(
        ?string $slider
    ) {
        $this->slider = $slider;
    }

    /**
     * @param BlockEvent $event
     */
    public function onBlockEvent(BlockEvent $event): void
    {
        $template = null;

        if (null !== $this->slider) {
            $template = '@OdiseoSyliusBannerPlugin/Shop/Slider/_'.$this->slider.'_js.html.twig';
        }

        if (null !== $template) {
            $block = new Block();
            $block->setId(uniqid('', true));
            $block->setSettings(array_replace($event->getSettings(), [
                'template' => $template,
            ]));
            $block->setType('sonata.block.service.template');

            $event->addBlock($block);
        }
    }
}
