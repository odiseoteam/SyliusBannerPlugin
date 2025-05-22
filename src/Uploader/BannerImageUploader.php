<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Uploader;

use Gaufrette\FilesystemInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Symfony\Component\HttpFoundation\File\File;

final class BannerImageUploader implements BannerImageUploaderInterface
{
    public function __construct(
        private FilesystemInterface $filesystem,
    ) {
    }

    public function upload(BannerTranslationInterface $bannerTranslation): void
    {
        $imageTypes = [
            'image' => 'ImageName',
            'mobileImage' => 'MobileImageName',
        ];

        foreach ($imageTypes as $prefix => $nameSuffix) {
            $getFileMethod = 'get' . ucfirst($prefix) . 'File';
            $getNameMethod = 'get' . $nameSuffix;
            $setNameMethod = 'set' . $nameSuffix;

            if (!method_exists($bannerTranslation, $getFileMethod)) {
                continue;
            }

            /** @var File|null $file */
            $file = $bannerTranslation->$getFileMethod();

            // Skip if no file uploaded and no name exists
            if ($file === null && $bannerTranslation->$getNameMethod() === null) {
                continue;
            }

            // Remove existing file if needed
            if (null !== $bannerTranslation->$getNameMethod() && $this->has($bannerTranslation->$getNameMethod())) {
                $this->remove($bannerTranslation->$getNameMethod());
            }

            if ($file === null) {
                continue; // No new file uploaded
            }

            // Generate a new path
            do {
                $path = $this->name($file);
            } while ($this->isAdBlockingProne($path) || $this->filesystem->has($path));

            $bannerTranslation->$setNameMethod($path);

            // Check if file content is valid before writing
            $fileContents = file_get_contents($file->getPathname());
            if ($fileContents === false) {
                continue;
            }

            $this->filesystem->write($path, $fileContents, true);
        }
    }

    public function remove(string $path): bool
    {
        if ($this->filesystem->has($path)) {
            return $this->filesystem->delete($path);
        }

        return false;
    }

    private function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }

    private function name(File $file): string
    {
        $name = \str_replace('.', '', \uniqid('', true));
        $extension = $file->guessExtension();

        if (\is_string($extension) && '' !== $extension) {
            $name = \sprintf('%s.%s', $name, $extension);
        }

        return $name;
    }

    private function isAdBlockingProne(string $path): bool
    {
        return str_contains($path, 'ad');
    }
}
