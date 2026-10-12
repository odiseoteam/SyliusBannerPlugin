<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Support;

use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslation;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Currency\Model\Currency;
use Sylius\Component\Locale\Model\Locale;

/** A store of its own inside a transaction that is rolled back after each test. */
trait StoreTrait
{
    private EntityManagerInterface $entityManager;

    private function openStore(): void
    {
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);

        $this->entityManager = $entityManager;
        $this->entityManager->getConnection()->beginTransaction();
        $this->entityManager->getConnection()->executeStatement('DELETE FROM odiseo_banner');
    }

    private function closeStore(): void
    {
        $this->entityManager->getConnection()->rollBack();
    }

    /** The images stored by the test are not rolled back with the transaction. */
    private function removeBannerImages(FilesystemOperator $storage): void
    {
        $this->entityManager->clear();

        foreach ($this->entityManager->getRepository(Banner::class)->findAll() as $banner) {
            foreach ($banner->getTranslations() as $translation) {
                if (!$translation instanceof BannerTranslation) {
                    continue;
                }

                foreach (array_filter([$translation->getImageName(), $translation->getMobileImageName()]) as $path) {
                    $storage->delete($path);
                }
            }
        }
    }

    private function createChannel(string $code, string $hostname): Channel
    {
        $locale = $this->entityManager->getRepository(Locale::class)->findOneBy(['code' => 'en_US']) ?? new Locale();
        $locale->setCode('en_US');
        $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['code' => 'USD']) ?? new Currency();
        $currency->setCode('USD');

        $channel = new Channel();
        $channel->setCode($code);
        $channel->setName($code);
        $channel->setHostname($hostname);
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setDefaultLocale($locale);
        $channel->addLocale($locale);
        $channel->setBaseCurrency($currency);
        $channel->addCurrency($currency);

        $this->entityManager->persist($locale);
        $this->entityManager->persist($currency);
        $this->entityManager->persist($channel);
        $this->entityManager->flush();

        return $channel;
    }

    /** Behat leaves its own taxons behind in the test database: keep these codes apart from them. */
    private function createTaxon(string $code, ?Taxon $parent = null): Taxon
    {
        $taxon = new Taxon();
        $taxon->setCode($code);
        $taxon->setParent($parent);
        $taxon->setCurrentLocale('en_US');
        $taxon->setFallbackLocale('en_US');
        $taxon->setName($code);
        $taxon->setSlug(null === $parent ? $code : $parent->getSlug() . '/' . $code);

        $this->entityManager->persist($taxon);
        $this->entityManager->flush();

        return $taxon;
    }

    /** @param array{position?: int, enabled?: bool, taxon?: Taxon, main_text?: string, secondary_text?: string, url?: string, button_text?: string, mobile_image?: string} $options */
    private function createBanner(string $code, ?Channel $channel = null, array $options = []): Banner
    {
        $translation = new BannerTranslation();
        $translation->setLocale('en_US');
        $translation->setImageName($code . '.png');
        $translation->setMobileImageName($options['mobile_image'] ?? null);
        $translation->setMainText($options['main_text'] ?? null);
        $translation->setSecondaryText($options['secondary_text'] ?? null);
        $translation->setUrl($options['url'] ?? null);
        $translation->setButtonText($options['button_text'] ?? null);

        $banner = new Banner();
        $banner->setCode($code);
        $banner->setCurrentLocale('en_US');
        $banner->setFallbackLocale('en_US');
        $banner->setPosition($options['position'] ?? 0);
        $banner->setEnabled($options['enabled'] ?? true);
        $banner->addTranslation($translation);

        if (null !== $channel) {
            $banner->addChannel($channel);
        }
        if (isset($options['taxon'])) {
            $banner->addTaxon($options['taxon']);
        }

        $this->entityManager->persist($banner);
        $this->entityManager->flush();

        return $banner;
    }
}
