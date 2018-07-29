<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Resource\Model\TimestampableTrait;

class BannerType implements BannerTypeInterface
{
    use TimestampableTrait;

    /** @var int */
    protected $id;

    /** @var string */
    protected $name;

    /** @var ArrayCollection|BannerInterface[] */
    protected $banners;

    public function __construct()
    {
        $this->banners = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getBanners()
    {
        return $this->banners;
    }

    /**
     * {@inheritdoc}
     */
    public function setBanners(ArrayCollection $banners)
    {
        $this->banners = $banners;
    }
    /**
     * {@inheritdoc}
     */
    public function addBanner(BannerInterface $banner)
    {
        if(!$this->banners->contains($banner))
            $this->banners->add($banner);
    }
    /**
     * {@inheritdoc}
     */
    public function removeBanner(BannerInterface $banner)
    {
        if($this->banners->contains($banner))
            $this->banners->removeElement($banner);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
