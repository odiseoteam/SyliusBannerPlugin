<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Integration\Fixture;

use Sylius\Bundle\FixturesBundle\Suite\SuiteRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/** The banners loaded with the default fixtures of Sylius. */
final class DefaultBannersTest extends KernelTestCase
{
    public function testTheDefaultSuiteLoadsFourBannersForTheHomepageInTheirOrder(): void
    {
        $banners = array_values(array_filter(
            $this->defaultBanners(),
            static fn (array $banner): bool => [] === ($banner['taxons'] ?? []),
        ));

        self::assertSame(
            ['new_collection', 'summer_sale', 'free_shipping', 'new_arrivals'],
            array_column($banners, 'code'),
        );
        self::assertSame([1, 2, 3, 4], array_column($banners, 'position'));
    }

    public function testTheDefaultSuiteLoadsOneBannerForEachMainTaxon(): void
    {
        $taxons = [];
        foreach ($this->defaultBanners() as $banner) {
            foreach ((array) ($banner['taxons'] ?? []) as $taxon) {
                $taxons[] = $taxon;
            }
        }

        self::assertSame(['t_shirts', 'caps', 'dresses', 'jeans'], $taxons);
    }

    public function testEveryImageOfTheDefaultBannersIsShipped(): void
    {
        $kernel = self::bootKernel();

        foreach ($this->defaultBanners() as $banner) {
            foreach (['image', 'mobile_image'] as $option) {
                $path = $banner[$option] ?? null;
                if (null === $path) {
                    continue;
                }

                self::assertIsString($path);
                self::assertFileExists($kernel->locateResource($path));
            }
        }
    }

    /** @return list<array<mixed>> */
    private function defaultBanners(): array
    {
        $registry = self::getContainer()->get('sylius_fixtures.suite_registry');
        self::assertInstanceOf(SuiteRegistryInterface::class, $registry);

        foreach ($registry->getSuite('default')->getFixtures() as $fixture => $options) {
            if ('banner' !== $fixture->getName()) {
                continue;
            }

            $banners = $options['custom'] ?? null;
            self::assertIsArray($banners);

            $list = [];
            foreach ($banners as $banner) {
                self::assertIsArray($banner);
                $list[] = $banner;
            }

            return $list;
        }

        self::fail('The default suite has no banner fixture.');
    }
}
