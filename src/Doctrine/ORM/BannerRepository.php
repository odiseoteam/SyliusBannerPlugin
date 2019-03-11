<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class BannerRepository extends EntityRepository implements BannerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByEnabledQueryBuilder(ChannelInterface $channel, TaxonInterface $taxon = null)
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->innerJoin('b.channels', 'channel')
            ->andWhere('b.enabled = :enabled')
            ->andWhere('channel = :channel')
            ->setParameter('channel', $channel)
            ->setParameter('enabled', true)
        ;

        if ($taxon) {
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
    public function findByChannel(ChannelInterface $channel)
    {
        return $this->findByEnabledQueryBuilder($channel)->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByChannelAndTaxon(ChannelInterface $channel, TaxonInterface $taxon)
    {
        return $this->findByEnabledQueryBuilder($channel, $taxon)->getQuery()->getResult();
    }
}
