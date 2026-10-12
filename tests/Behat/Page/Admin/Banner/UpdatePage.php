<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use FormTrait;

    public function isCodeDisabled(): bool
    {
        return $this->getElement('code')->hasAttribute('disabled');
    }
}
