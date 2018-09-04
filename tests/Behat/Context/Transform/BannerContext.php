<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class BannerContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $bannerRepository;

    /**
     * @param RepositoryInterface $bannerRepository
     */
    public function __construct(
        RepositoryInterface $bannerRepository
    ) {
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @Transform /^banner "([^"]+)"$/
     * @Transform /^"([^"]+)" banner$/
     */
    public function getBannerByCode($bannerCode)
    {
        $banner = $this->bannerRepository->findOneBy(['code' => $bannerCode]);

        Assert::notNull(
            $banner,
            'Banner with code %s does not exist'
        );

        return $banner;
    }
}
