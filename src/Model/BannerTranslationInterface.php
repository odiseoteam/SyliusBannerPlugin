<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Symfony\Component\HttpFoundation\File\File;

interface BannerTranslationInterface extends ResourceInterface, TranslationInterface, TimestampableInterface
{
    /**
     * @param File $file
     */
    public function setImageFile(File $file);

    /**
     * @return File
     */
    public function getImageFile();

    /**
     * @param string $imageName
     */
    public function setImageName($imageName);

    /**
     * @return string
     */
    public function getImageName();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     */
    public function setUrl($url);
}
