<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface BannerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     *
     * @return QueryBuilder
     */
    public function findByEnabledQueryBuilder(ChannelInterface $channel, TaxonInterface $taxon);

    /**
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function findByChannel(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     *
     * @return array
     */
    public function findByChannelAndTaxon(ChannelInterface $channel, TaxonInterface $taxon);
}
