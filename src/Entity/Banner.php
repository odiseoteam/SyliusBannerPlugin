<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Entity;

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

    /** @var int|null */
    protected $id;

    /** @var string|null */
    protected $code;

    /**
     * @var Collection|ChannelInterface[]
     *
     * @psalm-var Collection<array-key, ChannelInterface>
     */
    protected $channels;

    /**
     * @var Collection|TaxonInterface[]
     *
     * @psalm-var Collection<array-key, TaxonInterface>
     */
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
    public function getId(): ?int
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
    public function setImageFile(?File $file): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setImageFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageFile(): ?File
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getImageFile();
    }

    /**
     * {@inheritdoc}
     */
    public function setImageName(?string $imageName): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setImageName($imageName);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageName(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getImageName();
    }

    /**
     * {@inheritdoc}
     */
    public function setMobileImageFile(?File $file): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setMobileImageFile($file);
    }

    /**
     * {@inheritdoc}
     */
    public function getMobileImageFile(): ?File
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getMobileImageFile();
    }

    /**
     * {@inheritdoc}
     */
    public function setMobileImageName(?string $mobileImageName): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setMobileImageName($mobileImageName);
    }

    /**
     * {@inheritdoc}
     */
    public function getMobileImageName(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getMobileImageName();
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl(?string $url): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function setMainText(?string $mainText): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setMainText($mainText);
    }

    /**
     * {@inheritdoc}
     */
    public function getMainText(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getMainText();
    }

    /**
     * {@inheritdoc}
     */
    public function setSecondaryText(?string $secondaryText): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setSecondaryText($secondaryText);
    }

    /**
     * {@inheritdoc}
     */
    public function getSecondaryText(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getSecondaryText();
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
    protected function createTranslation(): TranslationInterface
    {
        return new BannerTranslation();
    }
}
