<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Twig\Component\Shop;

use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Provider\TaxonBannersProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/** The banners of the taxon being browsed, or of its ancestors, taken from the slug of the request. */
class TaxonBannersComponent
{
    public function __construct(
        private readonly TaxonBannersProviderInterface $taxonBannersProvider,
        private readonly ChannelContextInterface $channelContext,
        private readonly RequestStack $requestStack,
        private readonly TaxonRepositoryInterface $taxonRepository,
        private readonly LocaleContextInterface $localeContext,
    ) {
    }

    /** @return array<BannerInterface> */
    #[ExposeInTemplate('banners')]
    public function getBanners(): array
    {
        $slug = $this->requestStack->getMainRequest()?->attributes->get('slug');
        if (!is_string($slug) || '' === $slug) {
            return [];
        }

        $taxon = $this->taxonRepository->findOneBySlug($slug, $this->localeContext->getLocaleCode());
        if (!$taxon instanceof TaxonInterface) {
            return [];
        }

        return $this->taxonBannersProvider->getBanners($this->channelContext->getChannel(), $taxon);
    }
}
