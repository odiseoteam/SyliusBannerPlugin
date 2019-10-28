<?php

declare(strict_types=1);

namespace spec\Odiseo\SyliusBannerPlugin\Entity;

use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Taxonomy\Model\TaxonsAwareInterface;

final class BannerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Banner::class);
    }

    function it_implements_banner_interface()
    {
        $this->shouldImplement(BannerInterface::class);
    }

    function it_implements_resource_interface()
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_implements_code_aware_interface()
    {
        $this->shouldImplement(CodeAwareInterface::class);
    }

    function it_implements_translatable_interface()
    {
        $this->shouldImplement(TranslatableInterface::class);
    }

    function it_implements_toggleable_interface()
    {
        $this->shouldImplement(ToggleableInterface::class);
    }

    function it_implements_channels_aware_interface()
    {
        $this->shouldImplement(ChannelsAwareInterface::class);
    }

    function it_implements_taxons_aware_interface()
    {
        $this->shouldImplement(TaxonsAwareInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function it_is_timestampable()
    {
        $dateTime = new \DateTime();
        $this->setCreatedAt($dateTime);
        $this->getCreatedAt()->shouldReturn($dateTime);
        $this->setUpdatedAt($dateTime);
        $this->getUpdatedAt()->shouldReturn($dateTime);
    }

    function it_allows_access_via_properties()
    {
        $this->setCode('banner');
        $this->getCode()->shouldReturn('banner');
    }
}
