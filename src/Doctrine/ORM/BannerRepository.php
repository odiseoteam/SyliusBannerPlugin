<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Odiseo\SyliusBannerPlugin\Model\ChannelInterface;
use Odiseo\SyliusBannerPlugin\Model\TaxonInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class BannerRepository extends EntityRepository implements BannerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByChannelQuery(ChannelInterface $channel, TaxonInterface $taxon = null)
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->innerJoin('b.channels', 'channel')
            ->andWhere('b.enabled = :enabled')
            ->andWhere('channel = :channel')
            ->setParameter('channel', $channel)
            ->setParameter('enabled', true)
        ;

        if($taxon) {
            $queryBuilder->innerJoin('b.taxons', 'taxon')
                ->andWhere('taxon = :taxon')
                ->setParameter('taxon', $taxon)
            ;
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function findByChannel(ChannelInterface $channel, TaxonInterface $taxon = null)
    {
        return $this->findByChannelQuery($channel, $taxon)->getQuery()->getResult();
    }
}
