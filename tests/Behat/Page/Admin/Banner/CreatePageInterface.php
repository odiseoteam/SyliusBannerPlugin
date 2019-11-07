<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Behaviour\ContainsErrorInterface;

interface CreatePageInterface extends BaseCreatePageInterface, ContainsErrorInterface
{
    /**
     * @param string $code
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillCode($code): void;

    /**
     * @param string $url
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function fillUrl($url): void;

    /**
     * @param string $file
     * @param string $locator
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function uploadFile($file, $locator): void;
}
