<?php

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Behaviour\ContainsErrorInterface;

interface CreatePageInterface extends BaseCreatePageInterface, ContainsErrorInterface
{
    /**
     * @param string $code
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillCode($code);

    /**
     * @param string $url
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillUrl($url);

    /**
     * @param string $file
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function uploadFile($file);
}
