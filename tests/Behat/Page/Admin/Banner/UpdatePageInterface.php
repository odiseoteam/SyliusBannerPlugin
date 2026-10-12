<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function specifyPosition(int $position): void;

    public function checkChannel(string $name): void;

    public function attachImage(string $path, string $localeCode): void;

    public function specifyMainText(string $mainText, string $localeCode): void;

    public function disable(): void;

    public function isCodeDisabled(): bool;

    public function getErrorOf(string $element, string $localeCode = ''): string;
}
