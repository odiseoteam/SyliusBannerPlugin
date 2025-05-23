<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\EventListener;

use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class BannerImageUploadListener
{
    public function __construct(
        private BannerImageUploaderInterface $uploader,
    ) {
    }

    public function uploadImage(GenericEvent $event): void
    {
        /** @var BannerInterface $banner */
        $banner = $event->getSubject();
        Assert::isInstanceOf($banner, BannerInterface::class);

        /** @var BannerTranslationInterface $translation */
        foreach ($banner->getTranslations() as $translation) {
            $this->uploader->upload($translation);
        }
    }
}
