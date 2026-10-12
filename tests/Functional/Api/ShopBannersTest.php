<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Functional\Api;

use Sylius\Component\Core\Model\Channel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Odiseo\SyliusBannerPlugin\Support\StoreTrait;

final class ShopBannersTest extends WebTestCase
{
    use StoreTrait;

    private const HOST = 'shop.banner.test';

    private KernelBrowser $browser;

    private Channel $channel;

    protected function setUp(): void
    {
        $this->browser = self::createClient(server: ['HTTP_HOST' => self::HOST, 'HTTP_ACCEPT' => 'application/json']);
        $this->browser->disableReboot();

        $this->openStore();
        $this->channel = $this->createChannel('SHOP', self::HOST);
    }

    protected function tearDown(): void
    {
        $this->closeStore();

        parent::tearDown();
    }

    public function testItListsTheBannersOfTheChannelInTheirOrder(): void
    {
        $other = $this->createChannel('OTHER', 'other.banner.test');
        $this->createBanner('second', $this->channel, ['position' => 2]);
        $this->createBanner('first', $this->channel, ['position' => 1]);
        $this->createBanner('disabled', $this->channel, ['enabled' => false]);
        $this->createBanner('other_channel', $other);

        $banners = $this->get('/api/v2/shop/banners');

        self::assertResponseIsSuccessful();
        self::assertSame(['first', 'second'], array_column($banners, 'code'));
    }

    public function testItGivesWhatIsNeededToRenderABanner(): void
    {
        $this->createBanner('summer_sale', $this->channel, [
            'position' => 2,
            'main_text' => 'Summer sale',
            'secondary_text' => 'Up to 50% off',
            'button_text' => 'Shop now',
            'url' => 'https://example.com/sale',
            'mobile_image' => 'summer_sale_mobile.png',
        ]);

        $banner = $this->get('/api/v2/shop/banners/summer_sale');

        self::assertResponseIsSuccessful();
        self::assertSame('summer_sale', $banner['code']);
        self::assertSame(2, $banner['position']);
        self::assertSame('Summer sale', $banner['mainText']);
        self::assertSame('Up to 50% off', $banner['secondaryText']);
        self::assertSame('Shop now', $banner['buttonText']);
        self::assertSame('https://example.com/sale', $banner['url']);
        self::assertIsString($banner['image']);
        self::assertStringContainsString('/media/cache/resolve/odiseo_banner_image/summer_sale.png', $banner['image']);
        self::assertIsString($banner['mobileImage']);
        self::assertStringContainsString('/media/cache/resolve/odiseo_banner_mobile_image/summer_sale_mobile.png', $banner['mobileImage']);
    }

    public function testItHasNoMobileImageWhenTheBannerHasNone(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $banner = $this->get('/api/v2/shop/banners/summer_sale');

        self::assertArrayHasKey('mobileImage', $banner);
        self::assertNull($banner['mobileImage']);
    }

    public function testItDoesNotExposeWhatOnlyTheAdministratorManages(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $banner = $this->get('/api/v2/shop/banners/summer_sale');

        self::assertArrayNotHasKey('enabled', $banner);
        self::assertArrayNotHasKey('channels', $banner);
        self::assertArrayNotHasKey('translations', $banner);
    }

    public function testItFiltersTheBannersByTaxon(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts]);
        $this->createBanner('home_banner', $this->channel);

        $banners = $this->get('/api/v2/shop/banners?taxons.code=shirts');

        self::assertSame(['shirts_banner'], array_column($banners, 'code'));
    }

    public function testItFiltersTheBannersThatAreNotTiedToATaxon(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts]);
        $this->createBanner('home_banner', $this->channel);

        $banners = $this->get('/api/v2/shop/banners?exists[taxons]=false');

        self::assertSame(['home_banner'], array_column($banners, 'code'));
    }

    public function testADisabledBannerIsNotFound(): void
    {
        $this->createBanner('disabled', $this->channel, ['enabled' => false]);

        $this->get('/api/v2/shop/banners/disabled');

        self::assertResponseStatusCodeSame(404);
    }

    public function testABannerOfAnotherChannelIsNotFound(): void
    {
        $other = $this->createChannel('OTHER', 'other.banner.test');
        $this->createBanner('other_channel', $other);

        $this->get('/api/v2/shop/banners/other_channel');

        self::assertResponseStatusCodeSame(404);
    }

    public function testTheShopCannotChangeBanners(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $this->browser->request('DELETE', '/api/v2/shop/banners/summer_sale');
        self::assertResponseStatusCodeSame(405);

        $this->browser->request('POST', '/api/v2/shop/banners');
        self::assertResponseStatusCodeSame(405);
    }

    /** @return array<mixed> */
    private function get(string $uri): array
    {
        $this->browser->request('GET', $uri);

        $decoded = json_decode((string) $this->browser->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        return $decoded;
    }
}
