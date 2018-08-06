<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\Taxon as BaseTaxon;

class Taxon extends BaseTaxon implements TaxonInterface
{
    /** @var ArrayCollection */
    protected $banners;

    public function __construct()
    {
        parent::__construct();

        $this->banners = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getBanners()
    {
        return $this->banners;
    }

    /**
     * @param ArrayCollection $banners
     */
    public function setBanners($banners)
    {
        $this->banners = $banners;
    }
}
