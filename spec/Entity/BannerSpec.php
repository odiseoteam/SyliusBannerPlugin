<?php

declare(strict_types=1);

namespace spec\Odiseo\SyliusBannerPlugin\Entity;

use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use PhpSpec\ObjectBehavior;

final class BannerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Banner::class);
    }

    public function it_implements_banner_interface(): void
    {
        $this->shouldImplement(BannerInterface::class);
    }

    public function it_has_position_zero_by_default(): void
    {
        $this->getPosition()->shouldReturn(0);
    }

    public function its_position_is_mutable(): void
    {
        $this->setPosition(5);

        $this->getPosition()->shouldReturn(5);
    }
}
