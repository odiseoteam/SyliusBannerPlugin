<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Uploader;

use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Symfony\Component\HttpFoundation\File\File;

final class BannerImageUploader implements BannerImageUploaderInterface
{
    public function __construct(
        private FilesystemOperator $filesystem,
    ) {
    }

    public function upload(BannerTranslationInterface $bannerTranslation): void
    {
        $imageName = $this->store($bannerTranslation->getImageFile(), $bannerTranslation->getImageName());
        if (null !== $imageName) {
            $bannerTranslation->setImageName($imageName);
        }

        $mobileImageName = $this->store($bannerTranslation->getMobileImageFile(), $bannerTranslation->getMobileImageName());
        if (null !== $mobileImageName) {
            $bannerTranslation->setMobileImageName($mobileImageName);
        }
    }

    public function remove(string $path): bool
    {
        if (!$this->filesystem->fileExists($path)) {
            return false;
        }

        $this->filesystem->delete($path);

        return true;
    }

    /** Returns the name of the stored file, or null when there is no new file to store. */
    private function store(?File $file, ?string $currentName): ?string
    {
        // Nothing to do until a new file comes in: removing the stored one first would
        // destroy it on any edit that does not upload an image again.
        if (null === $file) {
            return null;
        }

        $contents = file_get_contents($file->getPathname());
        if (false === $contents) {
            return null;
        }

        if (null !== $currentName) {
            $this->remove($currentName);
        }

        do {
            $path = $this->name($file);
        } while ($this->filesystem->fileExists($path));

        $this->filesystem->write($path, $contents);

        return $path;
    }

    private function name(File $file): string
    {
        $name = $this->withoutAdBlockingSubstring(\str_replace('.', '', \uniqid('', true)));
        $extension = $file->guessExtension();

        if (\is_string($extension) && '' !== $extension) {
            $name = \sprintf('%s.%s', $name, $extension);
        }

        return $name;
    }

    /** uniqid() starts with the timestamp in hexadecimal, so there are windows of hours in which every name would contain "ad". */
    private function withoutAdBlockingSubstring(string $name): string
    {
        return \str_replace('ad', 'a0', $name);
    }
}
