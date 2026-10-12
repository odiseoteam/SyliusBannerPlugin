<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;

final class BannerImagesContext implements Context
{
    public function __construct(
        private readonly BannerRepositoryInterface $bannerRepository,
        private readonly FilesystemOperator $storage,
    ) {
    }

    /**
     * The database is purged before each scenario, but the images it points at would stay.
     *
     * @AfterScenario
     */
    public function removeTheImagesOfTheScenario(): void
    {
        /** @var BannerInterface $banner */
        foreach ($this->bannerRepository->findAll() as $banner) {
            /** @var BannerTranslationInterface $translation */
            foreach ($banner->getTranslations() as $translation) {
                foreach (array_filter([$translation->getImageName(), $translation->getMobileImageName()]) as $path) {
                    $this->storage->delete($path);
                }
            }
        }
    }
}
