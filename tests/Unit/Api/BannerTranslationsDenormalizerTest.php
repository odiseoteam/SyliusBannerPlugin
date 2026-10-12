<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Api;

use Odiseo\SyliusBannerPlugin\Api\Serializer\BannerTranslationsDenormalizer;
use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslation;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class BannerTranslationsDenormalizerTest extends TestCase
{
    private Banner $banner;

    private BannerTranslation $english;

    protected function setUp(): void
    {
        $this->english = new BannerTranslation();
        $this->english->setLocale('en_US');
        $this->english->setImageName('stored.png');
        $this->english->setMainText('Summer sale');
        $this->english->setUrl('https://example.com/sale');

        $this->banner = new Banner();
        $this->banner->addTranslation($this->english);
    }

    public function testItOnlyHandlesABannerThatIsBeingUpdated(): void
    {
        $denormalizer = $this->denormalizer();

        self::assertTrue($denormalizer->supportsDenormalization([], Banner::class, null, $this->context()));
        self::assertFalse($denormalizer->supportsDenormalization([], Banner::class));
        self::assertFalse($denormalizer->supportsDenormalization([], BannerTranslation::class, null, $this->context()));
    }

    public function testItUpdatesAnExistingTranslationInPlace(): void
    {
        $this->denormalize(['translations' => ['en_US' => ['mainText' => 'Winter sale', 'url' => null]]]);

        self::assertSame([$this->english], $this->banner->getTranslations()->getValues());
        self::assertSame('Winter sale', $this->english->getMainText());
        self::assertNull($this->english->getUrl());
        self::assertSame('stored.png', $this->english->getImageName());
    }

    public function testItLeavesTheFieldsThatWereNotSentUntouched(): void
    {
        $this->denormalize(['translations' => ['en_US' => ['buttonText' => 'Shop now']]]);

        self::assertSame('Summer sale', $this->english->getMainText());
        self::assertSame('https://example.com/sale', $this->english->getUrl());
        self::assertSame('Shop now', $this->english->getButtonText());
    }

    public function testItAddsATranslationForANewLocale(): void
    {
        $this->denormalize(['translations' => ['es_ES' => ['mainText' => 'Rebajas de verano']]]);

        $spanish = $this->banner->getTranslations()->get('es_ES');
        self::assertInstanceOf(BannerTranslation::class, $spanish);
        self::assertSame('Rebajas de verano', $spanish->getMainText());
        self::assertSame('Summer sale', $this->english->getMainText());
    }

    /** The name of the stored file only changes through an upload. */
    public function testItIgnoresTheImageNames(): void
    {
        $this->denormalize(['translations' => ['en_US' => ['imageName' => 'other.png']]]);

        self::assertSame('stored.png', $this->english->getImageName());
    }

    public function testItRejectsATextThatIsNotAString(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $this->denormalize(['translations' => ['en_US' => ['mainText' => ['Summer sale']]]]);
    }

    public function testItRejectsTranslationsThatAreNotKeyedByLocale(): void
    {
        $this->expectException(NotNormalizableValueException::class);

        $this->denormalize(['translations' => [['mainText' => 'Summer sale']]]);
    }

    /** @param array<string, mixed> $data */
    private function denormalize(array $data): void
    {
        $this->denormalizer()->denormalize($data, Banner::class, null, $this->context());
    }

    /** @return array<string, mixed> */
    private function context(): array
    {
        return [AbstractNormalizer::OBJECT_TO_POPULATE => $this->banner];
    }

    private function denormalizer(): BannerTranslationsDenormalizer
    {
        $factory = $this->createMock(FactoryInterface::class);
        $factory->method('createNew')->willReturnCallback(static fn (): BannerTranslation => new BannerTranslation());

        $inner = $this->createMock(DenormalizerInterface::class);
        $inner->method('denormalize')->willReturnCallback(function (mixed $data): Banner {
            self::assertIsArray($data);
            self::assertArrayNotHasKey('translations', $data);

            return $this->banner;
        });

        $denormalizer = new BannerTranslationsDenormalizer($factory);
        $denormalizer->setDenormalizer($inner);

        return $denormalizer;
    }
}
