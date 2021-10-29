<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Symfony\Component\HttpFoundation\File\File;

interface BannerTranslationInterface extends
    ResourceInterface,
    TranslationInterface,
    TimestampableInterface
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
}
