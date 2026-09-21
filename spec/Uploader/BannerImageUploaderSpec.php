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

    public function it_keeps_the_stored_file_when_the_translation_is_saved_without_a_new_one(
        FilesystemInterface $filesystem,
        BannerTranslationInterface $bannerTranslation,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);
        $filesystem->has('stored.png')->willReturn(true);

        $bannerTranslation->getImageFile()->willReturn(null);
        $bannerTranslation->getImageName()->willReturn('stored.png');
        $bannerTranslation->getMobileImageFile()->willReturn(null);
        $bannerTranslation->getMobileImageName()->willReturn(null);

        $filesystem->delete(Argument::any())->willReturn(true)->shouldNotBeCalled();
        $filesystem->write(Argument::cetera())->shouldNotBeCalled();
        $bannerTranslation->setImageName(Argument::any())->shouldNotBeCalled();

        $this->upload($bannerTranslation);
    }

    public function it_keeps_the_stored_mobile_file_when_the_translation_is_saved_without_a_new_one(
        FilesystemInterface $filesystem,
        BannerTranslationInterface $bannerTranslation,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);
        $filesystem->has('stored-mobile.png')->willReturn(true);

        $bannerTranslation->getImageFile()->willReturn(null);
        $bannerTranslation->getImageName()->willReturn(null);
        $bannerTranslation->getMobileImageFile()->willReturn(null);
        $bannerTranslation->getMobileImageName()->willReturn('stored-mobile.png');

        $filesystem->delete(Argument::any())->willReturn(true)->shouldNotBeCalled();
        $filesystem->write(Argument::cetera())->shouldNotBeCalled();
        $bannerTranslation->setMobileImageName(Argument::any())->shouldNotBeCalled();

        $this->upload($bannerTranslation);
    }

    public function it_replaces_the_stored_file_when_a_new_one_is_uploaded(
        FilesystemInterface $filesystem,
        BannerTranslationInterface $bannerTranslation,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);
        $filesystem->has('stored.png')->willReturn(true);
        $filesystem->delete('stored.png')->willReturn(true);
        $filesystem->write(Argument::cetera())->willReturn(1);

        $bannerTranslation->getImageFile()->willReturn(new File(self::IMAGE));
        $bannerTranslation->getImageName()->willReturn('stored.png');
        $bannerTranslation->getMobileImageFile()->willReturn(null);
        $bannerTranslation->getMobileImageName()->willReturn(null);

        $filesystem->delete('stored.png')->shouldBeCalled();
        $bannerTranslation->setImageName(Argument::that(
            static fn (string $name): bool => 'stored.png' !== $name,
        ))->shouldBeCalled();
        $filesystem->write(Argument::cetera())->shouldBeCalled();

        $this->upload($bannerTranslation);
    }

    public function it_does_nothing_when_there_is_neither_a_stored_file_nor_a_new_one(
        FilesystemInterface $filesystem,
        BannerTranslationInterface $bannerTranslation,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);

        $bannerTranslation->getImageFile()->willReturn(null);
        $bannerTranslation->getImageName()->willReturn(null);
        $bannerTranslation->getMobileImageFile()->willReturn(null);
        $bannerTranslation->getMobileImageName()->willReturn(null);

        $filesystem->delete(Argument::any())->willReturn(true)->shouldNotBeCalled();
        $filesystem->write(Argument::cetera())->shouldNotBeCalled();

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
