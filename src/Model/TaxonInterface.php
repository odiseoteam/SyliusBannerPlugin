<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\TaxonInterface as BaseTaxonInterface;

interface TaxonInterface extends BaseTaxonInterface
{
    /**
     * @return ArrayCollection
     */
    public function getBanners();

    /**
     * @param ArrayCollection $vendors
     */
    public function setBanners($vendors);
}
