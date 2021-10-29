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

    protected ?int $id = null;
    protected ?string $code = null;

    /**
     * @psalm-var Collection<array-key, ChannelInterface>
     */
    protected Collection $channels;

    /**
     * @psalm-var Collection<array-key, TaxonInterface>
     */
    protected Collection $taxons;

    public function __construct()
    {
        $this->initializeTranslationsCollection();

        $this->channels = new ArrayCollection();
        $this->taxons = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function setImageFile(?File $file): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setImageFile($file);
    }

    public function getImageFile(): ?File
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getImageFile();
    }

    public function setImageName(?string $imageName): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setImageName($imageName);
    }

    public function getImageName(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getImageName();
    }

    public function setMobileImageFile(?File $file): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setMobileImageFile($file);
    }

    public function getMobileImageFile(): ?File
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getMobileImageFile();
    }

    public function setMobileImageName(?string $mobileImageName): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setMobileImageName($mobileImageName);
    }

    public function getMobileImageName(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getMobileImageName();
    }

    public function setUrl(?string $url): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setUrl($url);
    }

    public function getUrl(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getUrl();
    }

    public function setMainText(?string $mainText): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setMainText($mainText);
    }

    public function getMainText(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getMainText();
    }

    public function setSecondaryText(?string $secondaryText): void
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        $bannerTranslation->setSecondaryText($secondaryText);
    }

    public function getSecondaryText(): ?string
    {
        /** @var BannerTranslationInterface $bannerTranslation */
        $bannerTranslation = $this->getTranslation();

        return $bannerTranslation->getSecondaryText();
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function hasChannel(ChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }

    public function addChannel(ChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(ChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function getTaxons(): Collection
    {
        return $this->taxons;
    }

    public function hasTaxon(TaxonInterface $taxon): bool
    {
        return $this->taxons->contains($taxon);
    }

    public function addTaxon(TaxonInterface $taxon): void
    {
        if (!$this->taxons->contains($taxon)) {
            $this->taxons->add($taxon);
        }
    }

    public function removeTaxon(TaxonInterface $taxon): void
    {
        if ($this->taxons->contains($taxon)) {
            $this->taxons->removeElement($taxon);
        }
    }

    public function getTranslation(?string $locale = null): TranslationInterface
    {
        /** @var BannerTranslation $translation */
        $translation = $this->doGetTranslation($locale);

        return $translation;
    }

    protected function createTranslation(): TranslationInterface
    {
        return new BannerTranslation();
    }
}
