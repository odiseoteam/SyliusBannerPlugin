<?php

namespace spec\Odiseo\SyliusBannerPlugin\Model;

use Odiseo\SyliusBannerPlugin\Model\Banner;
use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Taxonomy\Model\TaxonsAwareInterface;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
class BannerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Banner::class);
    }

    function it_implements_banner_interface(): void
    {
        $this->shouldImplement(BannerInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_implements_code_aware_interface(): void
    {
        $this->shouldImplement(CodeAwareInterface::class);
    }

    function it_implements_translatable_interface(): void
    {
        $this->shouldImplement(TranslatableInterface::class);
    }

    function it_implements_toggleable_interface(): void
    {
        $this->shouldImplement(ToggleableInterface::class);
    }

    function it_implements_channels_aware_interface(): void
    {
        $this->shouldImplement(ChannelsAwareInterface::class);
    }

    function it_implements_taxons_aware_interface(): void
    {
        $this->shouldImplement(TaxonsAwareInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function it_is_timestampable(): void
    {
        $dateTime = new \DateTime();
        $this->setCreatedAt($dateTime);
        $this->getCreatedAt()->shouldReturn($dateTime);
        $this->setUpdatedAt($dateTime);
        $this->getUpdatedAt()->shouldReturn($dateTime);
    }

    function it_allows_access_via_properties(): void
    {
        $this->setCode('banner');
        $this->getCode()->shouldReturn('banner');
    }
}
