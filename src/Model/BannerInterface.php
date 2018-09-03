<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
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
    ChannelsAwareInterface,
    TaxonsAwareInterface
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

    /**
     * @param string|null $locale
     *
     * @return BannerTranslationInterface
     */
    public function getTranslation(?string $locale = null): TranslationInterface;
}
