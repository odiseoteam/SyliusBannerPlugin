<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code): void;

    public function specifyPosition(int $position): void;

    public function checkChannel(string $name): void;

    public function attachImage(string $path, string $localeCode): void;

    public function specifyMainText(string $mainText, string $localeCode): void;

    public function disable(): void;

    public function getErrorOf(string $element, string $localeCode = ''): string;
}
