<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Symfony\Component\HttpFoundation\File\File;

class BannerTranslation extends AbstractTranslation implements BannerTranslationInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var File */
    protected $imageFile;

    /** @var string */
    protected $imageName;

    /** @var string */
    protected $url;

    public function __construct()
    {
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
    public function setImageFile(File $file)
    {
        $this->imageFile = $file;

        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;
    }
    /**
     * {@inheritdoc}
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
}
