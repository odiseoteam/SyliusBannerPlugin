<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Functional\Shop;

use Sylius\Component\Core\Model\Channel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Odiseo\SyliusBannerPlugin\Support\StoreTrait;

final class DisplayingBannersTest extends WebTestCase
{
    use StoreTrait;

    private const HOST = 'shop.banner.test';

    private const TEMPLATE = '@OdiseoSyliusBannerPlugin/shop/banner/content/index.html.twig';

    private KernelBrowser $browser;

    private Channel $channel;

    protected function setUp(): void
    {
        $this->browser = self::createClient(server: ['HTTP_HOST' => self::HOST]);
        $this->browser->disableReboot();

        $this->openStore();
        $this->channel = $this->createChannel('SHOP', self::HOST);
    }

    protected function tearDown(): void
    {
        $this->closeStore();

        parent::tearDown();
    }

    public function testTheHomepageShowsTheBannerOfTheChannel(): void
    {
        $this->createBanner('summer_sale', $this->channel, ['main_text' => 'Summer sale']);

        $crawler = $this->browser->request('GET', '/en_US/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('.banner-image-container .main-text', 'Summer sale');
        self::assertStringContainsString(
            '/media/cache/resolve/odiseo_banner_image/summer_sale.png',
            (string) $crawler->filter('.banner-image-container img')->attr('src'),
        );
    }

    public function testTheHomepageLeavesOutTheBannersOfATaxon(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('home_banner', $this->channel, ['main_text' => 'Home']);
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts, 'main_text' => 'Shirts']);

        $crawler = $this->browser->request('GET', '/en_US/');

        self::assertResponseIsSuccessful();
        self::assertSame(['Home'], $crawler->filter('.banner-image-container .main-text')->each(static fn (Crawler $node): string => $node->text()));
    }

    public function testTheTaxonPageShowsTheBannersOfItsTaxon(): void
    {
        $shirts = $this->createTaxon('shirts');
        $hats = $this->createTaxon('hats');
        $this->createBanner('home_banner', $this->channel, ['main_text' => 'Home']);
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts, 'main_text' => 'Shirts']);
        $this->createBanner('hats_banner', $this->channel, ['taxon' => $hats, 'main_text' => 'Hats']);

        $crawler = $this->browser->request('GET', '/en_US/taxons/shirts');

        self::assertResponseIsSuccessful();
        self::assertSame(['Shirts'], $crawler->filter('.banner-image-container .main-text')->each(static fn (Crawler $node): string => $node->text()));
    }

    public function testATaxonWithoutBannersShowsTheOnesOfItsParent(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createTaxon('mens_shirts', $shirts);
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts, 'main_text' => 'Shirts']);

        $crawler = $this->browser->request('GET', '/en_US/taxons/shirts/mens_shirts');

        self::assertResponseIsSuccessful();
        self::assertSame(['Shirts'], $crawler->filter('.banner-image-container .main-text')->each(static fn (Crawler $node): string => $node->text()));
    }

    public function testATaxonWithItsOwnBannersDoesNotShowTheOnesOfItsParent(): void
    {
        $shirts = $this->createTaxon('shirts');
        $mensShirts = $this->createTaxon('mens_shirts', $shirts);
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts, 'main_text' => 'Shirts']);
        $this->createBanner('mens_shirts_banner', $this->channel, ['taxon' => $mensShirts, 'main_text' => 'Men']);

        $crawler = $this->browser->request('GET', '/en_US/taxons/shirts/mens_shirts');

        self::assertResponseIsSuccessful();
        self::assertSame(['Men'], $crawler->filter('.banner-image-container .main-text')->each(static fn (Crawler $node): string => $node->text()));
    }

    public function testTheTaxonPageHasNoBannerWhenItsTaxonHasNone(): void
    {
        $this->createTaxon('shirts');
        $this->createBanner('home_banner', $this->channel);

        $this->browser->request('GET', '/en_US/taxons/shirts');

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('.banner-image-container');
    }

    public function testTheHomepageHasNoBannerWhenThereIsNone(): void
    {
        $this->browser->request('GET', '/en_US/');

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('.banner-image-container');
    }

    public function testBannersOfOtherChannelsAndDisabledOnesAreNotShown(): void
    {
        $other = $this->createChannel('OTHER', 'other.banner.test');
        $this->createBanner('other_channel', $other, ['main_text' => 'Other channel']);
        $this->createBanner('disabled', $this->channel, ['main_text' => 'Disabled', 'enabled' => false]);

        $this->browser->request('GET', '/en_US/banners/partial', ['template' => self::TEMPLATE]);

        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('.banner-image-container');
    }

    public function testASingleBannerIsShownWithoutASlider(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $this->browser->request('GET', '/en_US/banners/partial', ['template' => self::TEMPLATE]);

        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, '.banner-image-container');
        self::assertSelectorNotExists('.swiper');
    }

    public function testSeveralBannersAreShownInASliderInTheirOrder(): void
    {
        $this->createBanner('second', $this->channel, ['position' => 2, 'main_text' => 'Second']);
        $this->createBanner('first', $this->channel, ['position' => 1, 'main_text' => 'First']);

        $crawler = $this->browser->request('GET', '/en_US/banners/partial', ['template' => self::TEMPLATE]);

        self::assertResponseIsSuccessful();
        self::assertSame(
            ['First', 'Second'],
            $crawler->filter('#homepage-main-banner .swiper-slide .main-text')->each(static fn (Crawler $node): string => $node->text()),
        );
    }

    public function testTheMobileImageReplacesTheMainOneOnSmallScreens(): void
    {
        $this->createBanner('summer_sale', $this->channel, ['mobile_image' => 'summer_sale_mobile.png']);

        $crawler = $this->browser->request('GET', '/en_US/banners/partial', ['template' => self::TEMPLATE]);

        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            '/media/cache/resolve/odiseo_banner_mobile_image/summer_sale_mobile.png',
            (string) $crawler->filter('img.d-md-none')->attr('src'),
        );
        self::assertSelectorExists('img.d-none.d-md-block');
    }

    public function testTheWholeBannerLinksToItsUrlWhenItHasNoButton(): void
    {
        $this->createBanner('summer_sale', $this->channel, ['url' => 'https://example.com/sale']);

        $crawler = $this->browser->request('GET', '/en_US/banners/partial', ['template' => self::TEMPLATE]);

        self::assertSame('https://example.com/sale', $crawler->filter('.banner-image-container > a')->attr('href'));
        self::assertSelectorExists('.banner-image-container > a img');
    }

    public function testTheButtonLinksToTheUrlWhenTheBannerHasOne(): void
    {
        $this->createBanner('summer_sale', $this->channel, ['url' => 'https://example.com/sale', 'button_text' => 'Shop now']);

        $crawler = $this->browser->request('GET', '/en_US/banners/partial', ['template' => self::TEMPLATE]);

        $button = $crawler->filter('.banner-image-container > a.button');
        self::assertSame('Shop now', $button->text());
        self::assertSame('https://example.com/sale', $button->attr('href'));
        self::assertSelectorNotExists('.banner-image-container > a img');
    }

    public function testTheBannersOfATaxonAreServedApart(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts, 'main_text' => 'Shirts']);
        $this->createBanner('home_banner', $this->channel, ['main_text' => 'Home']);

        $crawler = $this->browser->request('GET', '/en_US/banners/partial/by-taxon', ['template' => self::TEMPLATE, 'taxon' => 'shirts']);

        self::assertResponseIsSuccessful();
        self::assertSame(['Shirts'], $crawler->filter('.main-text')->each(static fn (Crawler $node): string => $node->text()));
    }
}
