<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class BannerContext implements Context
{
    public const STORED_IMAGE = 'stored image';

    public function __construct(
        private readonly FactoryInterface $bannerFactory,
        private readonly ObjectManager $bannerManager,
        private readonly FilesystemOperator $storage,
        private readonly SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Given there is a banner :code
     * @Given there is a banner :code with the main text :mainText
     * @Given there is a banner :code with the main text :mainText at position :position
     */
    public function thereIsABanner(string $code, ?string $mainText = null, int $position = 0): void
    {
        $channel = $this->sharedStorage->get('channel');
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $this->createBanner($code, $channel, $mainText, $position);
    }

    /**
     * @Given /^there is a banner "([^"]+)" with the main text "([^"]+)" in the ("[^"]+" channel)$/
     */
    public function thereIsABannerInTheChannel(string $code, string $mainText, ChannelInterface $channel): void
    {
        $this->createBanner($code, $channel, $mainText);
    }

    /**
     * @Given /^there is a banner "([^"]+)" with the main text "([^"]+)" for the ("[^"]+" taxon)$/
     */
    public function thereIsABannerForTheTaxon(string $code, string $mainText, TaxonInterface $taxon): void
    {
        $channel = $this->sharedStorage->get('channel');
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $this->createBanner($code, $channel, $mainText, 0, true, $taxon);
    }

    /**
     * @Given there is a disabled banner :code with the main text :mainText
     */
    public function thereIsADisabledBanner(string $code, string $mainText): void
    {
        $channel = $this->sharedStorage->get('channel');
        Assert::isInstanceOf($channel, ChannelInterface::class);

        $this->createBanner($code, $channel, $mainText, 0, false);
    }

    private function createBanner(
        string $code,
        ChannelInterface $channel,
        ?string $mainText = null,
        int $position = 0,
        bool $enabled = true,
        ?TaxonInterface $taxon = null,
    ): void {
        $imageName = $code . '.png';
        $this->storage->write($imageName, self::STORED_IMAGE);

        $banner = $this->bannerFactory->createNew();
        Assert::isInstanceOf($banner, BannerInterface::class);
        $banner->setCode($code);
        $banner->setPosition($position);
        $banner->setEnabled($enabled);
        $banner->addChannel($channel);
        if (null !== $taxon) {
            $banner->addTaxon($taxon);
        }

        foreach ($channel->getLocales() as $locale) {
            $banner->setCurrentLocale((string) $locale->getCode());
            $banner->setFallbackLocale((string) $locale->getCode());
            $banner->setImageName($imageName);
            $banner->setMainText($mainText);
        }

        $this->bannerManager->persist($banner);
        $this->bannerManager->flush();

        $this->sharedStorage->set('banner', $banner);
    }
}
