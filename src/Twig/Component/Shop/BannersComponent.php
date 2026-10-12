<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Twig\Component\Shop;

use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Provider\TaxonBannersProviderInterface;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class BannersComponent
{
    /** The banners of this taxon, or of its ancestors, when set; otherwise the ones that are not tied to any taxon. */
    public ?TaxonInterface $taxon = null;

    public function __construct(
        private readonly BannerRepositoryInterface $bannerRepository,
        private readonly ChannelContextInterface $channelContext,
        private readonly TaxonBannersProviderInterface $taxonBannersProvider,
    ) {
    }

    /** @return array<BannerInterface> */
    #[ExposeInTemplate('banners')]
    public function getBanners(): array
    {
        $channel = $this->channelContext->getChannel();

        return null === $this->taxon
            ? $this->bannerRepository->findWithoutTaxonByChannel($channel)
            : $this->taxonBannersProvider->getBanners($channel, $this->taxon)
        ;
    }
}
