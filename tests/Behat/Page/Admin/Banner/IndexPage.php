<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * @inheritdoc
     */
    public function deleteBanner(string $code): void
    {
        $this->deleteResourceOnPage(['code' => $code]);
    }
}
