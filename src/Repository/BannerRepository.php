<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

class BannerRepository extends EntityRepository implements BannerRepositoryInterface
{
    public function findByEnabledQueryBuilder(?ChannelInterface $channel, ?TaxonInterface $taxon): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->andWhere('b.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('b.position', 'ASC')
            ->addOrderBy('b.id', 'ASC')
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

    public function findByChannel(ChannelInterface $channel): array
    {
        /** @var array<BannerInterface> $banners */
        $banners = $this->findByEnabledQueryBuilder($channel, null)->getQuery()->getResult();

        return $banners;
    }

    public function findWithoutTaxonByChannel(ChannelInterface $channel): array
    {
        /** @var array<BannerInterface> $banners */
        $banners = $this->findByEnabledQueryBuilder($channel, null)
            ->andWhere('b.taxons IS EMPTY')
            ->getQuery()
            ->getResult()
        ;

        return $banners;
    }

    public function findByTaxon(TaxonInterface $taxon): array
    {
        /** @var array<BannerInterface> $banners */
        $banners = $this->findByEnabledQueryBuilder(null, $taxon)->getQuery()->getResult();

        return $banners;
    }

    public function findByChannelAndTaxon(ChannelInterface $channel, TaxonInterface $taxon): array
    {
        /** @var array<BannerInterface> $banners */
        $banners = $this->findByEnabledQueryBuilder($channel, $taxon)->getQuery()->getResult();

        return $banners;
    }
}
