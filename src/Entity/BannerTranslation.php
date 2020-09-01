<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Entity;

use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;

class BannerTranslation extends AbstractTranslation implements BannerTranslationInterface
{
    use TimestampableTrait;

    /** @var int|null */
    protected $id;

    /** @var File|null */
    protected $imageFile;

    /** @var string|null */
    protected $imageName;

    /** @var File|null */
    protected $mobileImageFile;

    /** @var string|null */
    protected $mobileImageName;

    /** @var string|null */
    protected $url;

    /** @var string|null */
    protected $mainText;

    /** @var string|null */
    protected $secondaryText;

    public function __construct()
    {
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
    public function setImageFile(?File $file): void
    {
        $this->imageFile = $file;

        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }
    /**
     * {@inheritdoc}
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * {@inheritdoc}
     */
    public function setMobileImageFile(?File $file): void
    {
        $this->mobileImageFile = $file;

        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function getMobileImageFile(): ?File
    {
        return $this->mobileImageFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setMobileImageName(?string $mobileImageName): void
    {
        $this->mobileImageName = $mobileImageName;
    }
    /**
     * {@inheritdoc}
     */
    public function getMobileImageName(): ?string
    {
        return $this->mobileImageName;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $mainText
     */
    public function setMainText(?string $mainText): void
    {
        $this->mainText = $mainText;
    }

    /**
     * @return string|null
     */
    public function getMainText(): ?string
    {
        return $this->mainText;
    }

    /**
     * @param string|null $secondaryText
     */
    public function setSecondaryText(?string $secondaryText): void
    {
        $this->secondaryText = $secondaryText;
    }

    /**
     * @return string|null
     */
    public function getSecondaryText(): ?string
    {
        return $this->secondaryText;
    }
}
