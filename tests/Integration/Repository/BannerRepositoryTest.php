<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Integration\Repository;

use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Odiseo\SyliusBannerPlugin\Support\StoreTrait;

final class BannerRepositoryTest extends KernelTestCase
{
    use StoreTrait;

    private BannerRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->openStore();

        $repository = self::getContainer()->get('odiseo_banner.repository.banner');
        self::assertInstanceOf(BannerRepositoryInterface::class, $repository);
        $this->repository = $repository;
    }

    protected function tearDown(): void
    {
        $this->closeStore();

        parent::tearDown();
    }

    public function testItFindsTheBannersOfAChannel(): void
    {
        $web = $this->createChannel('WEB', 'web.banner.test');
        $mobile = $this->createChannel('MOBILE', 'mobile.banner.test');
        $this->createBanner('web_banner', $web);
        $this->createBanner('mobile_banner', $mobile);
        $this->createBanner('orphan_banner');

        self::assertSame(['web_banner'], $this->codes($this->repository->findByChannel($web)));
    }

    public function testItLeavesDisabledBannersOut(): void
    {
        $web = $this->createChannel('WEB', 'web.banner.test');
        $this->createBanner('enabled_banner', $web);
        $this->createBanner('disabled_banner', $web, ['enabled' => false]);

        self::assertSame(['enabled_banner'], $this->codes($this->repository->findByChannel($web)));
    }

    public function testItOrdersTheBannersByPositionAndThenByCreation(): void
    {
        $web = $this->createChannel('WEB', 'web.banner.test');
        $this->createBanner('third', $web, ['position' => 2]);
        $this->createBanner('first', $web, ['position' => 1]);
        $this->createBanner('second', $web, ['position' => 1]);

        self::assertSame(['first', 'second', 'third'], $this->codes($this->repository->findByChannel($web)));
    }

    public function testItFindsTheBannersOfATaxon(): void
    {
        $web = $this->createChannel('WEB', 'web.banner.test');
        $shirts = $this->createTaxon('shirts');
        $hats = $this->createTaxon('hats');
        $this->createBanner('shirts_banner', $web, ['taxon' => $shirts]);
        $this->createBanner('hats_banner', $web, ['taxon' => $hats]);
        $this->createBanner('home_banner', $web);

        self::assertSame(['shirts_banner'], $this->codes($this->repository->findByTaxon($shirts)));
    }

    public function testItFindsTheBannersThatAreNotTiedToATaxon(): void
    {
        $web = $this->createChannel('WEB', 'web.banner.test');
        $mobile = $this->createChannel('MOBILE', 'mobile.banner.test');
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('home_banner', $web);
        $this->createBanner('shirts_banner', $web, ['taxon' => $shirts]);
        $this->createBanner('disabled_banner', $web, ['enabled' => false]);
        $this->createBanner('mobile_banner', $mobile);

        self::assertSame(['home_banner'], $this->codes($this->repository->findWithoutTaxonByChannel($web)));
    }

    public function testItFindsTheBannersOfATaxonInAChannel(): void
    {
        $web = $this->createChannel('WEB', 'web.banner.test');
        $mobile = $this->createChannel('MOBILE', 'mobile.banner.test');
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('web_shirts_banner', $web, ['taxon' => $shirts]);
        $this->createBanner('mobile_shirts_banner', $mobile, ['taxon' => $shirts]);
        $this->createBanner('web_home_banner', $web);

        self::assertSame(['web_shirts_banner'], $this->codes($this->repository->findByChannelAndTaxon($web, $shirts)));
    }

    /**
     * @param array<mixed> $banners
     *
     * @return list<string|null>
     */
    private function codes(array $banners): array
    {
        return array_values(array_map(static function (mixed $banner): ?string {
            self::assertInstanceOf(BannerInterface::class, $banner);

            return $banner->getCode();
        }, $banners));
    }
}
