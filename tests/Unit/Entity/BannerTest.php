<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Entity;

use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Component\HttpFoundation\File\File;

final class BannerTest extends TestCase
{
    private const IMAGE = __DIR__ . '/../../../config/app/fixtures/images/banner1.jpg';

    public function testItImplementsTheBannerInterface(): void
    {
        self::assertInstanceOf(BannerInterface::class, new Banner());
    }

    public function testItHasPositionZeroByDefault(): void
    {
        self::assertSame(0, (new Banner())->getPosition());
    }

    public function testItsPositionIsMutable(): void
    {
        $banner = new Banner();
        $banner->setPosition(5);

        self::assertSame(5, $banner->getPosition());
    }

    public function testItIsCreatedWithACreationDate(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, (new Banner())->getCreatedAt());
    }

    public function testItDoesNotAddTheSameChannelTwice(): void
    {
        $banner = new Banner();
        $channel = new Channel();

        $banner->addChannel($channel);
        $banner->addChannel($channel);

        self::assertCount(1, $banner->getChannels());
        self::assertTrue($banner->hasChannel($channel));
    }

    public function testItRemovesAChannel(): void
    {
        $banner = new Banner();
        $channel = new Channel();
        $banner->addChannel($channel);

        $banner->removeChannel($channel);

        self::assertFalse($banner->hasChannel($channel));
    }

    public function testItDoesNotAddTheSameTaxonTwice(): void
    {
        $banner = new Banner();
        $taxon = new Taxon();

        $banner->addTaxon($taxon);
        $banner->addTaxon($taxon);

        self::assertCount(1, $banner->getTaxons());
        self::assertTrue($banner->hasTaxon($taxon));
    }

    public function testItRemovesATaxon(): void
    {
        $banner = new Banner();
        $taxon = new Taxon();
        $banner->addTaxon($taxon);

        $banner->removeTaxon($taxon);

        self::assertFalse($banner->hasTaxon($taxon));
    }

    public function testItKeepsItsContentInTheTranslationOfTheCurrentLocale(): void
    {
        $banner = $this->bannerIn('en_US');
        $file = new File(self::IMAGE);

        $banner->setMainText('Summer sale');
        $banner->setSecondaryText('Up to 50% off');
        $banner->setButtonText('Shop now');
        $banner->setUrl('https://example.com/sale');
        $banner->setImageName('image.png');
        $banner->setMobileImageName('mobile.png');
        $banner->setImageFile($file);
        $banner->setMobileImageFile($file);

        $translation = $banner->getTranslation('en_US');
        self::assertInstanceOf(BannerTranslationInterface::class, $translation);
        self::assertSame('Summer sale', $translation->getMainText());
        self::assertSame('Up to 50% off', $translation->getSecondaryText());
        self::assertSame('Shop now', $translation->getButtonText());
        self::assertSame('https://example.com/sale', $translation->getUrl());
        self::assertSame('image.png', $translation->getImageName());
        self::assertSame('mobile.png', $translation->getMobileImageName());
        self::assertSame($file, $translation->getImageFile());
        self::assertSame($file, $translation->getMobileImageFile());
    }

    public function testItReadsItsContentFromTheCurrentLocale(): void
    {
        $banner = $this->bannerIn('en_US');
        $banner->setMainText('Summer sale');

        // Without a translation in the current locale, the one of the fallback locale is used.
        $banner->setCurrentLocale('es_ES');
        $banner->setFallbackLocale('es_ES');
        $banner->setMainText('Rebajas de verano');

        self::assertSame('Rebajas de verano', $banner->getMainText());

        $banner->setCurrentLocale('en_US');

        self::assertSame('Summer sale', $banner->getMainText());
    }

    private function bannerIn(string $locale): Banner
    {
        $banner = new Banner();
        $banner->setCurrentLocale($locale);
        $banner->setFallbackLocale($locale);

        return $banner;
    }
}
