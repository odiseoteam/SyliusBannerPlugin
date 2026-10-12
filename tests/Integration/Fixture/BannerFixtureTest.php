<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Integration\Fixture;

use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\Fixture\Factory\BannerExampleFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Odiseo\SyliusBannerPlugin\Support\StoreTrait;

/** The fixture goes through the same upload as the admin panel. */
final class BannerFixtureTest extends KernelTestCase
{
    use StoreTrait;

    private FilesystemOperator $storage;

    /** @var list<string> */
    private array $stored = [];

    protected function setUp(): void
    {
        $this->openStore();

        $storage = self::getContainer()->get('odiseo_banner.storage.banner_image');
        self::assertInstanceOf(FilesystemOperator::class, $storage);
        $this->storage = $storage;
    }

    protected function tearDown(): void
    {
        foreach ($this->stored as $path) {
            $this->storage->delete($path);
        }

        $this->closeStore();

        parent::tearDown();
    }

    public function testItStoresTheImagesOfTheBannerItCreates(): void
    {
        $channel = $this->createChannel('WEB', 'web.banner.test');

        $factory = self::getContainer()->get('odiseo_banner.fixture.example_factory.banner');
        self::assertInstanceOf(BannerExampleFactory::class, $factory);

        $banner = $factory->create(['code' => 'summer_sale', 'channels' => [$channel]]);

        $translation = $banner->getTranslation('en_US');
        self::assertInstanceOf(BannerTranslationInterface::class, $translation);
        $this->stored = array_values(array_filter([$translation->getImageName(), $translation->getMobileImageName()]));

        self::assertCount(2, $this->stored);
        foreach ($this->stored as $path) {
            self::assertTrue($this->storage->fileExists($path));
        }

        $this->entityManager->persist($banner);
        $this->entityManager->flush();

        self::assertNotNull($banner->getId());
    }
}
