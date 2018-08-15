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
    public function findByChannelQuery(ChannelInterface $channel, TaxonInterface $taxon);

    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     *
     * @return array
     */
    public function findByChannel(ChannelInterface $channel, TaxonInterface $taxon);
}
