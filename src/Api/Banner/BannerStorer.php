<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Api\Banner;

use ApiPlatform\Validator\Exception\ValidationException;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Odiseo\SyliusBannerPlugin\Uploader\BannerImageUploaderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** Validates a banner coming with uploaded files and stores its images, as the admin form does. */
final class BannerStorer
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly BannerImageUploaderInterface $uploader,
    ) {
    }

    public function store(BannerInterface $banner): void
    {
        $validation = $this->validator->startContext();
        $validation->validate($banner, null, ['odiseo']);

        /** @var BannerTranslationInterface $translation */
        foreach ($banner->getTranslations() as $translation) {
            $groups = null === $translation->getImageName() ? ['odiseo', 'odiseo_image_create'] : ['odiseo'];

            $validation
                ->atPath(sprintf('translations[%s]', (string) $translation->getLocale()))
                ->validate($translation, null, $groups)
            ;
        }

        $violations = $validation->getViolations();
        if (0 !== count($violations)) {
            throw new ValidationException($violations);
        }

        /** @var BannerTranslationInterface $translation */
        foreach ($banner->getTranslations() as $translation) {
            $this->uploader->upload($translation);
        }
    }
}
