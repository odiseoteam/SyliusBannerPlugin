<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Unit\Uploader;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslation;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploader;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploaderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\HttpFoundation\File\File;

final class BannerImageUploaderTest extends TestCase
{
    private const IMAGE = __DIR__ . '/../../../config/app/fixtures/images/banner1.jpg';

    private string $directory;

    private Filesystem $filesystem;

    private BannerImageUploader $uploader;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/odiseo_banner_' . bin2hex(random_bytes(6));
        $this->filesystem = new Filesystem(new LocalFilesystemAdapter($this->directory));
        $this->uploader = new BannerImageUploader($this->filesystem);
    }

    protected function tearDown(): void
    {
        (new SymfonyFilesystem())->remove($this->directory);
    }

    public function testItImplementsTheUploaderInterface(): void
    {
        self::assertInstanceOf(BannerImageUploaderInterface::class, $this->uploader);
    }

    public function testItStoresTheUploadedImageUnderItsNewName(): void
    {
        $translation = new BannerTranslation();
        $translation->setImageFile(new File(self::IMAGE));

        $this->uploader->upload($translation);

        $name = $translation->getImageName();
        self::assertNotNull($name);
        self::assertSame(file_get_contents(self::IMAGE), $this->filesystem->read($name));
        self::assertNull($translation->getMobileImageName());
    }

    public function testItStoresTheMobileImageApartFromTheMainOne(): void
    {
        $translation = new BannerTranslation();
        $translation->setImageFile(new File(self::IMAGE));
        $translation->setMobileImageFile(new File(self::IMAGE));

        $this->uploader->upload($translation);

        $name = $translation->getImageName();
        $mobileName = $translation->getMobileImageName();
        self::assertNotNull($name);
        self::assertNotNull($mobileName);
        self::assertNotSame($name, $mobileName);
        self::assertTrue($this->filesystem->fileExists($mobileName));
    }

    public function testItKeepsTheFileExtension(): void
    {
        $translation = new BannerTranslation();
        $translation->setImageFile(new File(self::IMAGE));

        $this->uploader->upload($translation);

        self::assertStringEndsWith('.jpg', (string) $translation->getImageName());
    }

    /** uniqid() starts with the timestamp in hexadecimal, which contains "ad" for hours at a time. */
    public function testItNamesTheImageSoThatAdBlockersDoNotHideIt(): void
    {
        $translation = new BannerTranslation();
        $translation->setImageFile(new File(self::IMAGE));

        $this->uploader->upload($translation);

        self::assertStringNotContainsString('ad', (string) $translation->getImageName());
    }

    public function testItKeepsTheStoredFileWhenTheTranslationIsSavedWithoutANewOne(): void
    {
        $this->filesystem->write('stored.png', 'stored');
        $translation = new BannerTranslation();
        $translation->setImageName('stored.png');

        $this->uploader->upload($translation);

        self::assertSame('stored.png', $translation->getImageName());
        self::assertSame('stored', $this->filesystem->read('stored.png'));
    }

    public function testItKeepsTheStoredMobileFileWhenTheTranslationIsSavedWithoutANewOne(): void
    {
        $this->filesystem->write('stored-mobile.png', 'stored');
        $translation = new BannerTranslation();
        $translation->setMobileImageName('stored-mobile.png');

        $this->uploader->upload($translation);

        self::assertSame('stored-mobile.png', $translation->getMobileImageName());
        self::assertSame('stored', $this->filesystem->read('stored-mobile.png'));
    }

    public function testItReplacesTheStoredFileWhenANewOneIsUploaded(): void
    {
        $this->filesystem->write('stored.png', 'stored');
        $translation = new BannerTranslation();
        $translation->setImageName('stored.png');
        $translation->setImageFile(new File(self::IMAGE));

        $this->uploader->upload($translation);

        $name = $translation->getImageName();
        self::assertNotNull($name);
        self::assertNotSame('stored.png', $name);
        self::assertFalse($this->filesystem->fileExists('stored.png'));
        self::assertTrue($this->filesystem->fileExists($name));
    }

    public function testItUploadsTheNewFileEvenWhenTheStoredOneIsGone(): void
    {
        $translation = new BannerTranslation();
        $translation->setImageName('gone.png');
        $translation->setImageFile(new File(self::IMAGE));

        $this->uploader->upload($translation);

        $name = $translation->getImageName();
        self::assertNotNull($name);
        self::assertNotSame('gone.png', $name);
        self::assertTrue($this->filesystem->fileExists($name));
    }

    public function testItDoesNothingWhenThereIsNeitherAStoredFileNorANewOne(): void
    {
        $translation = new BannerTranslation();

        $this->uploader->upload($translation);

        self::assertNull($translation->getImageName());
        self::assertNull($translation->getMobileImageName());
        self::assertSame([], $this->filesystem->listContents('')->toArray());
    }

    public function testItRemovesAStoredFile(): void
    {
        $this->filesystem->write('stored.png', 'stored');

        self::assertTrue($this->uploader->remove('stored.png'));
        self::assertFalse($this->filesystem->fileExists('stored.png'));
    }

    public function testItReportsThatThereWasNothingToRemove(): void
    {
        self::assertFalse($this->uploader->remove('missing.png'));
    }
}
