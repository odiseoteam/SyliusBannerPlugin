<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface BannerRepositoryInterface extends RepositoryInterface
{
    public function findByEnabledQueryBuilder(?ChannelInterface $channel, ?TaxonInterface $taxon): QueryBuilder;

    /** @return array<BannerInterface> */
    public function findByChannel(ChannelInterface $channel): array;

    /**
     * The banners that are not tied to any taxon, as shown on the homepage.
     *
     * @return array<BannerInterface>
     */
    public function findWithoutTaxonByChannel(ChannelInterface $channel): array;

    /** @return array<BannerInterface> */
    public function findByTaxon(TaxonInterface $taxon): array;

    /** @return array<BannerInterface> */
    public function findByChannelAndTaxon(ChannelInterface $channel, TaxonInterface $taxon): array;
}
