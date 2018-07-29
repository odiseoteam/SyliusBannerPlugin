<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Odiseo\SyliusBannerPlugin\Model\BannerTypeInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class BannerTypeRepository extends EntityRepository implements BannerTypeRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByName(string $name): ?BannerTypeInterface
    {
        $bannerType = $this->createQueryBuilder('o')
            ->andWhere('o.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $bannerType;
    }
}
