<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use Odiseo\SyliusBannerPlugin\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface BannerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return QueryBuilder
     */
    public function findByChannelQuery(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function findByChannel(ChannelInterface $channel);

    /**
     * @param string $code
     *
     * @return BannerInterface
     */
    public function findOneByCode(string $code): ?BannerInterface;
}