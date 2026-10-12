<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Api\Serializer;

use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Webmozart\Assert\Assert;

/**
 * Updates the translations of a banner in place, by locale. Replacing them, as the serializer
 * would, drops the images they point at.
 */
final class BannerTranslationsDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'odiseo_banner_translations_denormalizer_already_called';

    private const FIELDS = ['url', 'mainText', 'secondaryText', 'buttonText'];

    public function __construct(private readonly FactoryInterface $bannerTranslationFactory)
    {
    }

    /** @param array<string, mixed> $context */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $context[self::ALREADY_CALLED] = true;

        Assert::isArray($data);
        $translations = $data['translations'] ?? [];
        unset($data['translations']);

        $banner = $this->denormalizer->denormalize($data, $type, $format, $context);
        Assert::isInstanceOf($banner, BannerInterface::class);

        if (!is_array($translations)) {
            throw new NotNormalizableValueException('The translations have to be keyed by locale.');
        }

        foreach ($translations as $locale => $fields) {
            if (!is_string($locale) || !is_array($fields)) {
                throw new NotNormalizableValueException('The translations have to be keyed by locale.');
            }

            $this->update($this->translation($banner, $locale), $fields);
        }

        return $banner;
    }

    /** @param array<string, mixed> $context */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return
            is_array($data) &&
            is_a($type, BannerInterface::class, true) &&
            ($context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null) instanceof BannerInterface &&
            !isset($context[self::ALREADY_CALLED])
        ;
    }

    /** @return array<class-string, bool> */
    public function getSupportedTypes(?string $format): array
    {
        return [BannerInterface::class => false];
    }

    private function translation(BannerInterface $banner, string $locale): BannerTranslationInterface
    {
        $translation = $banner->getTranslations()->get($locale);
        if ($translation instanceof BannerTranslationInterface) {
            return $translation;
        }

        $translation = $this->bannerTranslationFactory->createNew();
        Assert::isInstanceOf($translation, BannerTranslationInterface::class);

        $translation->setLocale($locale);
        $banner->addTranslation($translation);

        return $translation;
    }

    /** @param array<mixed> $fields */
    private function update(BannerTranslationInterface $translation, array $fields): void
    {
        foreach (self::FIELDS as $field) {
            if (!array_key_exists($field, $fields)) {
                continue;
            }

            $value = $fields[$field];
            if (null !== $value && !is_string($value)) {
                throw new NotNormalizableValueException(sprintf('The "%s" of a translation has to be a string or null.', $field));
            }

            match ($field) {
                'url' => $translation->setUrl($value),
                'mainText' => $translation->setMainText($value),
                'secondaryText' => $translation->setSecondaryText($value),
                'buttonText' => $translation->setButtonText($value),
            };
        }
    }
}
