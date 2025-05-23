<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Uploader;

use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;

interface BannerImageUploaderInterface
{
    public function upload(BannerTranslationInterface $bannerTranslation): void;

    public function remove(string $path): bool;
}
