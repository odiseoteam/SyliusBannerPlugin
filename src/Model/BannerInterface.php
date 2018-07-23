<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface BannerInterface extends TranslatableInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return ArrayCollection|ChannelInterface[]
     */
    public function getChannels();

    /**
     * @param ArrayCollection $channel
     */
    public function setChannels(ArrayCollection $channel);

    /**
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     */
    public function removeChannel(ChannelInterface $channel);

    /**
     * @return string
     */
    public function __toString();
}