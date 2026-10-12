<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Api\StateProvider\Admin;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * A multipart request is not deserialized, and API Platform 4.1 needs something to work on.
 *
 * @implements ProviderInterface<BannerInterface>
 */
final class NewBannerProvider implements ProviderInterface
{
    public function __construct(private readonly FactoryInterface $bannerFactory)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): BannerInterface
    {
        $banner = $this->bannerFactory->createNew();
        Assert::isInstanceOf($banner, BannerInterface::class);

        return $banner;
    }
}
