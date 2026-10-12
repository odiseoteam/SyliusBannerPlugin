<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Provider;

use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface TaxonBannersProviderInterface
{
    /**
     * The banners of the taxon or, when it has none, the ones of its closest ancestor that has any.
     *
     * @return array<BannerInterface>
     */
    public function getBanners(ChannelInterface $channel, TaxonInterface $taxon): array;
}
