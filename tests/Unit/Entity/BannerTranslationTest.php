<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Entity;

use Odiseo\SyliusBannerPlugin\Entity\BannerTranslation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

final class BannerTranslationTest extends TestCase
{
    private const IMAGE = __DIR__ . '/../../../config/app/fixtures/images/banner1.jpg';

    public function testItHasNoImagesByDefault(): void
    {
        $translation = new BannerTranslation();

        self::assertNull($translation->getImageFile());
        self::assertNull($translation->getImageName());
        self::assertNull($translation->getMobileImageFile());
        self::assertNull($translation->getMobileImageName());
    }

    /** Doctrine only sees a change, and so only fires the upload, when a mapped field changes. */
    public function testSettingAnImageFileMarksItAsUpdated(): void
    {
        $translation = new BannerTranslation();
        self::assertNull($translation->getUpdatedAt());

        $translation->setImageFile(new File(self::IMAGE));

        self::assertInstanceOf(\DateTimeInterface::class, $translation->getUpdatedAt());
    }

    public function testSettingAMobileImageFileMarksItAsUpdated(): void
    {
        $translation = new BannerTranslation();

        $translation->setMobileImageFile(new File(self::IMAGE));

        self::assertInstanceOf(\DateTimeInterface::class, $translation->getUpdatedAt());
    }
}
