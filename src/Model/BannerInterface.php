<?php

namespace Odiseo\SyliusBannerPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

interface BannerInterface extends BannerTranslationInterface, TranslatableInterface, ToggleableInterface
{
    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return ArrayCollection|ChannelInterface[]
     */
    public function getChannels();

    /**
     * @param ArrayCollection $channels
     */
    public function setChannels(ArrayCollection $channels);

    /**
     * @param ChannelInterface $channel
     */
    public function addChannel(ChannelInterface $channel);

    /**
     * @param ChannelInterface $channel
     */
    public function removeChannel(ChannelInterface $channel);

    /**
     * @return ArrayCollection|TaxonInterface[]
     */
    public function getTaxons();

    /**
     * @param ArrayCollection $taxons
     */
    public function setTaxons(ArrayCollection $taxons);

    /**
     * @param TaxonInterface $taxon
     */
    public function addTaxon(TaxonInterface $taxon);

    /**
     * @param TaxonInterface $taxon
     */
    public function removeTaxon(TaxonInterface $taxon);

    /**
     * @return string
     */
    public function __toString();
}
