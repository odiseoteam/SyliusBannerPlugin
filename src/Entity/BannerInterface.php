<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Entity;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Taxonomy\Model\TaxonsAwareInterface;
use Symfony\Component\HttpFoundation\File\File;

interface BannerInterface extends
    ResourceInterface,
    CodeAwareInterface,
    TranslatableInterface,
    ToggleableInterface,
    TimestampableInterface,
    ChannelsAwareInterface,
    TaxonsAwareInterface
{
    public function setImageFile(?File $file): void;

    public function getImageFile(): ?File;

    public function setImageName(?string $imageName): void;

    public function getImageName(): ?string;

    public function setMobileImageFile(?File $file): void;

    public function getMobileImageFile(): ?File;

    public function setMobileImageName(?string $mobileImageName): void;

    public function getMobileImageName(): ?string;

    public function setUrl(?string $url): void;

    public function getUrl(): ?string;

    public function setMainText(?string $mainText): void;

    public function getMainText(): ?string;

    public function setSecondaryText(?string $secondaryText): void;

    public function getSecondaryText(): ?string;

    public function getTranslation(?string $locale = null): TranslationInterface;
}
