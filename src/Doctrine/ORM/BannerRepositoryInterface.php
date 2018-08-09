<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use Odiseo\SyliusBannerPlugin\Model\ChannelInterface;
use Odiseo\SyliusBannerPlugin\Model\TaxonInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface BannerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     *
     * @return QueryBuilder
     */
    public function findByChannelQuery(ChannelInterface $channel, TaxonInterface $taxon);

    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     *
     * @return array
     */
    public function findByChannel(ChannelInterface $channel, TaxonInterface $taxon);
}
