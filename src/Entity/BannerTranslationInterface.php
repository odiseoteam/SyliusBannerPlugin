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
}
