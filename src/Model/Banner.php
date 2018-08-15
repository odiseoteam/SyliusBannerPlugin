<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\File\File;

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

    /** @var ArrayCollection|ChannelInterface[] */
    protected $channels;

    /** @var ArrayCollection|TaxonInterface[] */
    protected $taxons;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->channels = new ArrayCollection();
        $this->taxons = new ArrayCollection();
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
     * {@inheritdoc}
     */
    public function getImageFile()
    {
        return $this->getTranslation()->getImageFile();
    }

    /**
     * {@inheritdoc}
     */
    public function setImageFile(File $file)
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
     * {@inheritdoc}
     */
    public function getTaxons()
    {
        return $this->taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function setTaxons(ArrayCollection $taxons)
    {
        $this->taxons = $taxons;
    }
    /**
     * {@inheritdoc}
     */
    public function addTaxon(TaxonInterface $taxon)
    {
        if(!$this->taxons->contains($taxon))
            $this->taxons->add($taxon);
    }
    /**
     * {@inheritdoc}
     */
    public function removeTaxon(TaxonInterface $taxon)
    {
        if($this->taxons->contains($taxon))
            $this->taxons->removeElement($taxon);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
}
