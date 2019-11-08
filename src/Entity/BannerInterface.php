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
    /**
     * @param File $file
     */
    public function setImageFile(File $file): void;

    /**
     * @return File
     */
    public function getImageFile(): ?File;

    /**
     * @param string $imageName
     */
    public function setImageName(string $imageName): void;

    /**
     * @return string
     */
    public function getImageName(): string;

    /**
     * @param File $file
     */
    public function setMobileImageFile(File $file): void;

    /**
     * @return File
     */
    public function getMobileImageFile(): ?File;

    /**
     * @param string $mobileImageName
     */
    public function setMobileImageName(string $mobileImageName): void;

    /**
     * @return string
     */
    public function getMobileImageName(): string;

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void;

    /**
     * @return string|null
     */
    public function getUrl(): ?string;

    /**
     * @param string|null $locale
     * @return TranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
