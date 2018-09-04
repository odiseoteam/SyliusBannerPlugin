<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\EntityManagerInterface;
use Odiseo\SyliusBannerPlugin\Doctrine\ORM\BannerRepositoryInterface;
use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class BannerContext implements Context
{
    /** @var FactoryInterface */
    private $bannerFactory;

    /** @var BannerRepositoryInterface */
    private $bannerRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        FactoryInterface $bannerFactory,
        BannerRepositoryInterface $bannerRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->bannerRepository = $bannerRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $code
     * @Given there is an existing banner with :code code
     */
    public function thereIsABannerWithCode(string $code): void
    {
        $banner = $this->createBanner($code);

        $this->saveBanner($banner);
    }

    /**
     * @param string $code
     *
     * @return BannerInterface
     */
    private function createBanner(string $code): BannerInterface
    {
        /** @var BannerInterface $banner */
        $banner = $this->bannerFactory->createNew();

        $banner->setCode($code);

        return $banner;
    }

    /**
     * @param BannerInterface $banner
     */
    private function saveBanner(BannerInterface $banner): void
    {
        $this->bannerRepository->add($banner);
    }
}
