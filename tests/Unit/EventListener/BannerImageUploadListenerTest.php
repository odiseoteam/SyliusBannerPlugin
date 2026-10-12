<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\EventListener;

use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslation;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\EventListener\BannerImageUploadListener;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploaderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\GenericEvent;

final class BannerImageUploadListenerTest extends TestCase
{
    public function testItUploadsTheImagesOfEveryTranslation(): void
    {
        $english = new BannerTranslation();
        $english->setLocale('en_US');
        $spanish = new BannerTranslation();
        $spanish->setLocale('es_ES');

        $banner = new Banner();
        $banner->addTranslation($english);
        $banner->addTranslation($spanish);

        $uploaded = [];
        $uploader = $this->createMock(BannerImageUploaderInterface::class);
        $uploader
            ->expects(self::exactly(2))
            ->method('upload')
            ->willReturnCallback(static function (BannerTranslationInterface $translation) use (&$uploaded): void {
                $uploaded[] = $translation;
            })
        ;

        (new BannerImageUploadListener($uploader))->uploadImage(new GenericEvent($banner));

        self::assertSame([$english, $spanish], $uploaded);
    }

    public function testItRejectsASubjectThatIsNotABanner(): void
    {
        $uploader = $this->createMock(BannerImageUploaderInterface::class);
        $uploader->expects(self::never())->method('upload');

        $this->expectException(\InvalidArgumentException::class);

        (new BannerImageUploadListener($uploader))->uploadImage(new GenericEvent(new \stdClass()));
    }
}
