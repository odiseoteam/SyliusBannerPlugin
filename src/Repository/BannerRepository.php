<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class BannerRepository extends EntityRepository implements BannerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByEnabledQueryBuilder(?ChannelInterface $channel, ?TaxonInterface $taxon): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->andWhere('b.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        if ($channel instanceof ChannelInterface) {
            $queryBuilder->innerJoin('b.channels', 'channel')
                ->andWhere('channel = :channel')
                ->setParameter('channel', $channel)
            ;
        }

        if ($taxon instanceof TaxonInterface) {
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
    public function findByChannel(ChannelInterface $channel): array
    {
        return $this->findByEnabledQueryBuilder($channel, null)->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByTaxon(TaxonInterface $taxon): array
    {
        return $this->findByEnabledQueryBuilder(null, $taxon)->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findByChannelAndTaxon(ChannelInterface $channel, TaxonInterface $taxon): array
    {
        return $this->findByEnabledQueryBuilder($channel, $taxon)->getQuery()->getResult();
    }
}
