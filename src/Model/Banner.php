<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\File\File;

class Banner implements BannerInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
        getTranslation as private doGetTranslation;
    }
    use TimestampableTrait;
    use ToggleableTrait;

    /** @var int */
    protected $id;

    /** @var string|null */
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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode(?string $code): void
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
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxons(): Collection
    {
        return $this->taxons;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTaxon(TaxonInterface $taxon): bool
    {
        return $this->taxons->contains($taxon);
    }

    /**
     * {@inheritdoc}
     */
    public function addTaxon(TaxonInterface $taxon): void
    {
        if (!$this->taxons->contains($taxon)) {
            $this->taxons->add($taxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxon(TaxonInterface $taxon): void
    {
        if ($this->taxons->contains($taxon)) {
            $this->taxons->removeElement($taxon);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var BannerTranslation $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    /**
     * {@inheritdoc}
     */
    protected function createTranslation(): BannerTranslation
    {
        return new BannerTranslation();
    }
}
