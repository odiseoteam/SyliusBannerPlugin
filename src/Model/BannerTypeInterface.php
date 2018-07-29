<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\ResourceInterface;

interface BannerTypeInterface extends ResourceInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    /**
     * @return ArrayCollection|BannerInterface[]
     */
    public function getBanners();

    /**
     * @param ArrayCollection $banner
     */
    public function setBanners(ArrayCollection $banner);

    /**
     * @param BannerInterface $banner
     */
    public function addBanner(BannerInterface $banner);

    /**
     * @param BannerInterface $banner
     */
    public function removeBanner(BannerInterface $banner);

    /**
     * @return string
     */
    public function __toString();
}
