<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;

class Banner implements BannerInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }
    use TimestampableTrait;
    use ToggleableTrait;

    /** @var int */
    protected $id;

    /** @var string */
    protected $code;

    /** @var BannerTypeInterface */
    protected $type;

    /** @var TaxonInterface */
    protected $taxon;

    /** @var ArrayCollection|ChannelInterface[] */
    protected $channels;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->channels = new ArrayCollection();
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
    protected function createTranslation()
    {
        return new BannerTranslation();
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(BannerTypeInterface $type)
    {
        $this->type = $type;
    }


    /**
     * {@inheritdoc}
     */
    public function getImageFile()
    {
        return $this->getTranslation()->getImageFile();
    }

    /**
     * {@inheritdoc}
     */
    public function setImageFile($file)
    {
        $this->getTranslation()->setImageFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageName()
    {
        return $this->getTranslation()->getImageName();
    }

    /**
     * {@inheritdoc}
     */
    public function setImageName($imageName)
    {
        $this->getTranslation()->setImageName($imageName);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->getTranslation()->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->getTranslation()->setUrl($url);
    }

    /**
     * @inheritdoc
     */
    public function getTaxon()
    {
        return $this->taxon;
    }

    /**
     * @inheritdoc
     */
    public function setTaxon(TaxonInterface $taxon)
    {
        $this->taxon = $taxon;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function setChannels(ArrayCollection $channels)
    {
        $this->channels = $channels;
    }
    /**
     * {@inheritdoc}
     */
    public function addChannel(ChannelInterface $channel)
    {
        if(!$this->channels->contains($channel))
            $this->channels->add($channel);
    }
    /**
     * {@inheritdoc}
     */
    public function removeChannel(ChannelInterface $channel)
    {
        if($this->channels->contains($channel))
            $this->channels->removeElement($channel);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
