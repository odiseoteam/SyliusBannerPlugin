<?php

namespace spec\Odiseo\SyliusBannerPlugin\Model;

use Odiseo\SyliusBannerPlugin\Model\BannerTranslation;
use Odiseo\SyliusBannerPlugin\Model\BannerTranslationInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Taxonomy\Model\TaxonsAwareInterface;

/**
 * @author Diego D'amico <diego@odiseo.com.ar>
 */
class BannerTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BannerTranslation::class);
    }

    function it_extends_abstract_translation(): void
    {
        $this->shouldImplement(AbstractTranslation::class);
    }

    function it_implements_banner_translation_interface(): void
    {
        $this->shouldImplement(BannerTranslationInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_implements_translation_interface(): void
    {
        $this->shouldImplement(TranslationInterface::class);
    }

    function it_implements_timestamplable_interface(): void
    {
        $this->shouldImplement(TimestampableInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_url_by_default(): void
    {
        $this->getUrl()->shouldReturn(null);
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
        $this->setUrl('banner_url');
        $this->getUrl()->shouldReturn('banner_url');
    }
}
