<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\Channel as BaseChannel;

class Channel extends BaseChannel implements ChannelInterface
{
    /** @var ArrayCollection */
    protected $banners;

    public function __construct()
    {
        parent::__construct();

        $this->banners = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getBanners()
    {
        return $this->banners;
    }

    /**
     * @param ArrayCollection $banners
     */
    public function setBanners($banners)
    {
        $this->banners = $banners;
    }
}