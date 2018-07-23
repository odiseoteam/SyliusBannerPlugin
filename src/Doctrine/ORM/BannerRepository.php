<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use Odiseo\SyliusBannerPlugin\Model\ChannelInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class BannerRepository extends EntityRepository implements BannerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByChannelQuery(ChannelInterface $channel)
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->innerJoin('b.channels', 'channel')
            ->andWhere('b.enabled = :enabled')
            ->andWhere('channel = :channel')
            ->setParameter('channel', $channel)
            ->setParameter('enabled', true)
        ;

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function findByChannel(ChannelInterface $channel)
    {
        return $this->findByChannelQuery($channel)->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCode(string $code): ?BannerInterface
    {
        $banner = $this->createQueryBuilder('o')
            ->andWhere('o.code = :code')
            ->andWhere('o.enabled = :enabled')
            ->setParameter('code', $code)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $banner;
    }
}