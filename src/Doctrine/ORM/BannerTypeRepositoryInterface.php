<?php

namespace Odiseo\SyliusBannerPlugin\Doctrine\ORM;

use Odiseo\SyliusBannerPlugin\Model\BannerTypeInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface BannerTypeRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $name
     *
     * @return BannerTypeInterface
     */
    public function findOneByName(string $name): ?BannerTypeInterface;
}
