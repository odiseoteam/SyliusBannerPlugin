<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface BannerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param ChannelInterface|null $channel
     * @param TaxonInterface|null $taxon
     * @return QueryBuilder
     */
    public function findByEnabledQueryBuilder(?ChannelInterface $channel, ?TaxonInterface $taxon): QueryBuilder;

    /**
     * @param ChannelInterface $channel
     * @return array
     */
    public function findByChannel(ChannelInterface $channel): array;

    /**
     * @param TaxonInterface $taxon
     * @return array
     */
    public function findByTaxon(TaxonInterface $taxon): array;

    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     * @return array
     */
    public function findByChannelAndTaxon(ChannelInterface $channel, TaxonInterface $taxon): array;
}
