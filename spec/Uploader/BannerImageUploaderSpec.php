<?php

declare(strict_types=1);

namespace spec\Odiseo\SyliusBannerPlugin\Uploader;

use Gaufrette\FilesystemInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploader;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploaderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\File\File;

final class BannerImageUploaderSpec extends ObjectBehavior
{
    private const IMAGE = __DIR__ . '/../../config/app/fixtures/images/banner1.png';

    public function let(FilesystemInterface $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    public function it_implements_banner_image_uploader_interface(): void
    {
        $this->shouldImplement(BannerImageUploaderInterface::class);
    }

    public function it_names_the_image_so_that_ad_blockers_do_not_hide_it(
        FilesystemInterface $filesystem,
        BannerTranslationInterface $bannerTranslation,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);
        $filesystem->write(Argument::cetera())->willReturn(1);

        $bannerTranslation->getImageFile()->willReturn(new File(self::IMAGE));
        $bannerTranslation->getImageName()->willReturn(null);
        $bannerTranslation->getMobileImageFile()->willReturn(null);
        $bannerTranslation->getMobileImageName()->willReturn(null);

        // uniqid() empieza con el timestamp en hexadecimal: hay ventanas de horas
        // en las que todo nombre generado contiene "ad".
        $bannerTranslation->setImageName(Argument::that(
            static fn (string $name): bool => !str_contains($name, 'ad'),
        ))->shouldBeCalled();

        $this->upload($bannerTranslation);
    }

    public function it_keeps_the_file_extension(
        FilesystemInterface $filesystem,
        BannerTranslationInterface $bannerTranslation,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);
        $filesystem->write(Argument::cetera())->willReturn(1);

        $bannerTranslation->getImageFile()->willReturn(new File(self::IMAGE));
        $bannerTranslation->getImageName()->willReturn(null);
        $bannerTranslation->getMobileImageFile()->willReturn(null);
        $bannerTranslation->getMobileImageName()->willReturn(null);

        $bannerTranslation->setImageName(Argument::that(
            static fn (string $name): bool => str_ends_with($name, '.png'),
        ))->shouldBeCalled();

        $this->upload($bannerTranslation);
    }
}
