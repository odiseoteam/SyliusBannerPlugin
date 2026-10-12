<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Provider;

use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonBannersProvider implements TaxonBannersProviderInterface
{
    public function __construct(private readonly BannerRepositoryInterface $bannerRepository)
    {
    }

    public function getBanners(ChannelInterface $channel, TaxonInterface $taxon): array
    {
        for ($current = $taxon; null !== $current; $current = $current->getParent()) {
            $banners = $this->bannerRepository->findByChannelAndTaxon($channel, $current);
            if ([] !== $banners) {
                return $banners;
            }
        }

        return [];
    }
}
