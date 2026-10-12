<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Fixture;

use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\Fixture\Factory\BannerExampleFactory;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class BannerExampleFactoryTest extends TestCase
{
    private Channel $channel;

    private Taxon $taxon;

    /** @var list<array{object, string}> */
    private array $dispatched = [];

    protected function setUp(): void
    {
        $this->channel = new Channel();
        $this->channel->setCode('WEB_US');
        $this->taxon = new Taxon();
        $this->taxon->setCode('t_shirts');
        $this->dispatched = [];
    }

    public function testItCreatesTheBannerWithItsChannelsAndTaxons(): void
    {
        $banner = $this->factory()->create(['code' => 'summer_sale', 'position' => 3, 'channels' => ['WEB_US'], 'taxons' => ['t_shirts']]);

        self::assertSame('summer_sale', $banner->getCode());
        self::assertSame(3, $banner->getPosition());
        self::assertSame([$this->channel], $banner->getChannels()->toArray());
        self::assertSame([$this->taxon], $banner->getTaxons()->toArray());
    }

    /** The default banners point at taxons a store may have removed. */
    public function testItLeavesOutATaxonTheStoreDoesNotHave(): void
    {
        $banner = $this->factory()->create(['code' => 'summer_sale', 'channels' => ['WEB_US'], 'taxons' => ['t_shirts', 'gone']]);

        self::assertSame([$this->taxon], $banner->getTaxons()->toArray());
    }

    public function testItShowsTheBannerOnEveryChannelWhenNoneIsSet(): void
    {
        $banner = $this->factory()->create(['code' => 'summer_sale', 'channels' => []]);

        self::assertSame([$this->channel], $banner->getChannels()->toArray());
    }

    public function testItTranslatesTheBannerToEveryLocaleOfTheStore(): void
    {
        $banner = $this->factory()->create([
            'code' => 'summer_sale',
            'channels' => ['WEB_US'],
            'main_text' => 'Summer sale',
            'secondary_text' => 'Up to 50% off',
            'button_text' => 'Shop now',
            'url' => 'https://example.com/sale',
        ]);

        self::assertSame(['en_US', 'es_ES'], $banner->getTranslations()->getKeys());
        foreach ($banner->getTranslations() as $translation) {
            self::assertInstanceOf(BannerTranslationInterface::class, $translation);
            self::assertSame('Summer sale', $translation->getMainText());
            self::assertSame('Up to 50% off', $translation->getSecondaryText());
            self::assertSame('Shop now', $translation->getButtonText());
            self::assertSame('https://example.com/sale', $translation->getUrl());
            self::assertInstanceOf(UploadedFile::class, $translation->getImageFile());
            self::assertInstanceOf(UploadedFile::class, $translation->getMobileImageFile());
        }
    }

    public function testItLeavesTheMobileImageOutWhenAskedTo(): void
    {
        $banner = $this->factory()->create(['code' => 'summer_sale', 'channels' => ['WEB_US'], 'mobile_image' => null]);

        foreach ($banner->getTranslations() as $translation) {
            self::assertInstanceOf(BannerTranslationInterface::class, $translation);
            self::assertInstanceOf(UploadedFile::class, $translation->getImageFile());
            self::assertNull($translation->getMobileImageFile());
        }
    }

    public function testItGivesTheBannerACodeAndAUrlWhenNoneIsSet(): void
    {
        $banner = $this->factory()->create(['channels' => ['WEB_US']]);

        self::assertNotEmpty($banner->getCode());
        $banner->setCurrentLocale('en_US');
        self::assertNotEmpty($banner->getUrl());
    }

    /** The images are stored by the listener of this event, as when creating a banner from the admin. */
    public function testItAnnouncesTheBannerSoThatItsImagesGetStored(): void
    {
        $banner = $this->factory()->create(['code' => 'summer_sale', 'channels' => ['WEB_US']]);

        self::assertCount(1, $this->dispatched);
        [$event, $name] = $this->dispatched[0];
        self::assertSame('odiseo_banner.banner.pre_create', $name);
        self::assertInstanceOf(GenericEvent::class, $event);
        self::assertSame($banner, $event->getSubject());
    }

    private function factory(): BannerExampleFactory
    {
        $bannerFactory = $this->createMock(FactoryInterface::class);
        $bannerFactory->method('createNew')->willReturnCallback(static fn (): Banner => new Banner());

        $channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $channelRepository->method('findOneBy')->with(['code' => 'WEB_US'])->willReturn($this->channel);
        $channelRepository->method('findAll')->willReturn([$this->channel]);

        $taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $taxonRepository->method('findOneBy')->willReturnCallback(
            fn (array $criteria): ?Taxon => ['code' => 't_shirts'] === $criteria ? $this->taxon : null,
        );

        $english = new Locale();
        $english->setCode('en_US');
        $spanish = new Locale();
        $spanish->setCode('es_ES');
        $localeRepository = $this->createMock(RepositoryInterface::class);
        $localeRepository->method('findAll')->willReturn([$english, $spanish]);

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->method('dispatch')->willReturnCallback(function (object $event, string $name): object {
            $this->dispatched[] = [$event, $name];

            return $event;
        });

        return new BannerExampleFactory($bannerFactory, $channelRepository, $taxonRepository, $localeRepository, $eventDispatcher);
    }
}
