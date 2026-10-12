<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Api\Serializer;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** Adds the URLs of the rendered images next to the names of the stored files. */
final class BannerImageNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'odiseo_banner_image_normalizer_already_called_for_%s';

    public function __construct(private readonly CacheManager $cacheManager)
    {
    }

    /**
     * @param array<string, mixed> $context
     *
     * @return array<mixed>|string|int|float|bool|\ArrayObject<array-key, mixed>|null
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $context[self::alreadyCalledKey($object)] = true;

        $data = $this->normalizer->normalize($object, $format, $context);
        if (!is_array($data)) {
            return $data;
        }

        if (array_key_exists('imageName', $data)) {
            $data['image'] = $this->url($data['imageName'], 'odiseo_banner_image');
        }
        if (array_key_exists('mobileImageName', $data)) {
            $data['mobileImage'] = $this->url($data['mobileImageName'], 'odiseo_banner_mobile_image');
        }

        return $data;
    }

    /** @param array<string, mixed> $context */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return
            ($data instanceof BannerInterface || $data instanceof BannerTranslationInterface) &&
            !isset($context[self::alreadyCalledKey($data)])
        ;
    }

    /** @return array<class-string, bool> */
    public function getSupportedTypes(?string $format): array
    {
        return [BannerInterface::class => false, BannerTranslationInterface::class => false];
    }

    private function url(mixed $name, string $filter): ?string
    {
        return is_string($name) && '' !== $name ? $this->cacheManager->getBrowserPath($name, $filter) : null;
    }

    private static function alreadyCalledKey(mixed $object): string
    {
        return sprintf(self::ALREADY_CALLED, is_object($object) ? spl_object_id($object) : '');
    }
}
