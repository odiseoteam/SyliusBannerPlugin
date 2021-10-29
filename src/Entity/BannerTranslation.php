<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Entity;

use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;

class BannerTranslation extends AbstractTranslation implements BannerTranslationInterface
{
    use TimestampableTrait;

    protected ?int $id = null;
    protected ?File $imageFile = null;
    protected ?string $imageName = null;
    protected ?File $mobileImageFile = null;
    protected ?string $mobileImageName = null;
    protected ?string $url = null;
    protected ?string $mainText = null;
    protected ?string $secondaryText = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setImageFile(?File $file): void
    {
        $this->imageFile = $file;

        $this->setUpdatedAt(new \DateTime());
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setMobileImageFile(?File $file): void
    {
        $this->mobileImageFile = $file;

        $this->setUpdatedAt(new \DateTime());
    }

    public function getMobileImageFile(): ?File
    {
        return $this->mobileImageFile;
    }

    public function setMobileImageName(?string $mobileImageName): void
    {
        $this->mobileImageName = $mobileImageName;
    }

    public function getMobileImageName(): ?string
    {
        return $this->mobileImageName;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setMainText(?string $mainText): void
    {
        $this->mainText = $mainText;
    }

    public function getMainText(): ?string
    {
        return $this->mainText;
    }

    public function setSecondaryText(?string $secondaryText): void
    {
        $this->secondaryText = $secondaryText;
    }

    public function getSecondaryText(): ?string
    {
        return $this->secondaryText;
    }
}
