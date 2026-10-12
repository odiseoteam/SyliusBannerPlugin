<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Provider;

use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Provider\TaxonBannersProvider;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Taxon;

final class TaxonBannersProviderTest extends TestCase
{
    private Taxon $category;

    private Taxon $shirts;

    private Taxon $mensShirts;

    protected function setUp(): void
    {
        $this->category = new Taxon();
        $this->shirts = new Taxon();
        $this->shirts->setParent($this->category);
        $this->mensShirts = new Taxon();
        $this->mensShirts->setParent($this->shirts);
    }

    public function testItGivesTheBannersOfTheTaxon(): void
    {
        $own = new Banner();
        $inherited = new Banner();

        $provider = $this->provider([[$this->mensShirts, [$own]], [$this->shirts, [$inherited]]]);

        self::assertSame([$own], $provider->getBanners(new Channel(), $this->mensShirts));
    }

    public function testATaxonWithoutBannersInheritsTheOnesOfItsParent(): void
    {
        $inherited = new Banner();

        $provider = $this->provider([[$this->shirts, [$inherited]]]);

        self::assertSame([$inherited], $provider->getBanners(new Channel(), $this->mensShirts));
    }

    public function testItInheritsFromTheClosestAncestorThatHasBanners(): void
    {
        $distant = new Banner();

        $provider = $this->provider([[$this->category, [$distant]]]);

        self::assertSame([$distant], $provider->getBanners(new Channel(), $this->mensShirts));
    }

    public function testItGivesNothingWhenNoAncestorHasBanners(): void
    {
        self::assertSame([], $this->provider([])->getBanners(new Channel(), $this->mensShirts));
    }

    /** @param list<array{Taxon, list<Banner>}> $bannersByTaxon */
    private function provider(array $bannersByTaxon): TaxonBannersProvider
    {
        $repository = $this->createMock(BannerRepositoryInterface::class);
        $repository->method('findByChannelAndTaxon')->willReturnCallback(
            static function (Channel $channel, Taxon $taxon) use ($bannersByTaxon): array {
                foreach ($bannersByTaxon as [$candidate, $banners]) {
                    if ($candidate === $taxon) {
                        return $banners;
                    }
                }

                return [];
            },
        );

        return new TaxonBannersProvider($repository);
    }
}
