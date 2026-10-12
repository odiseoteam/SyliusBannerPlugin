<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Api\StateProcessor\Admin;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Odiseo\SyliusBannerPlugin\Api\Banner\BannerStorer;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

/**
 * Replaces the images of one translation of a banner.
 *
 * @implements ProcessorInterface<mixed, BannerInterface>
 */
final class UploadBannerImagesProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<BannerInterface, BannerInterface> $persistProcessor
     */
    public function __construct(
        private readonly ProcessorInterface $persistProcessor,
        private readonly BannerRepositoryInterface $bannerRepository,
        private readonly FactoryInterface $bannerTranslationFactory,
        private readonly BannerStorer $bannerStorer,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BannerInterface
    {
        $request = $context['request'] ?? null;
        Assert::isInstanceOf($request, Request::class);

        $banner = $this->bannerRepository->findOneBy(['code' => $request->attributes->get('code')]);
        if (!$banner instanceof BannerInterface) {
            throw new NotFoundHttpException('Banner not found.');
        }

        $locale = $request->request->get('locale');
        if (!is_string($locale) || '' === $locale) {
            throw new BadRequestHttpException('The "locale" of the images is required.');
        }

        $image = $request->files->get('image');
        $mobileImage = $request->files->get('mobileImage');
        if (!$image instanceof UploadedFile && !$mobileImage instanceof UploadedFile) {
            throw new BadRequestHttpException('Send an "image", a "mobileImage" or both.');
        }

        $translation = $banner->getTranslations()->get($locale);
        if (!$translation instanceof BannerTranslationInterface) {
            $translation = $this->bannerTranslationFactory->createNew();
            Assert::isInstanceOf($translation, BannerTranslationInterface::class);

            $translation->setLocale($locale);
            $banner->addTranslation($translation);
        }

        if ($image instanceof UploadedFile) {
            $translation->setImageFile($image);
        }
        if ($mobileImage instanceof UploadedFile) {
            $translation->setMobileImageFile($mobileImage);
        }

        $this->bannerStorer->store($banner);

        return $this->persistProcessor->process($banner, $operation, $uriVariables, $context);
    }
}
