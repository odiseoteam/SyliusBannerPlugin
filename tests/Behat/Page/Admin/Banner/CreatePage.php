<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Behaviour\ContainsErrorTrait;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ContainsErrorTrait;

    /**
     * @inheritdoc
     */
    public function fillCode($code)
    {
        $this->getDocument()->fillField('Code', $code);
    }

    /**
     * @inheritdoc
     */
    public function fillUrl($url)
    {
        $this->getDocument()->fillField('URL', $url);
    }

    /**
     * {@inheritdoc}
     */
    public function uploadFile($file)
    {
        $path = __DIR__.'/../../../Resources/images/'.$file;
        Assert::fileExists($path);
        $this->getDocument()->attachFileToField('Image', realpath($path));
    }
}
