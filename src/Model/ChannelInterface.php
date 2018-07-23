<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface
{
    /**
     * @return ArrayCollection
     */
    public function getBanners();

    /**
     * @param ArrayCollection $vendors
     */
    public function setBanners($vendors);
}