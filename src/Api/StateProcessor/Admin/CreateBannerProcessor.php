<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Api\StateProcessor\Admin;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Odiseo\SyliusBannerPlugin\Api\Banner\BannerStorer;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerTranslationInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

/**
 * Creates a banner from a multipart request: the image is required, so it travels with the rest.
 *
 * @implements ProcessorInterface<mixed, BannerInterface>
 */
final class CreateBannerProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<BannerInterface, BannerInterface> $persistProcessor
     */
    public function __construct(
        private readonly ProcessorInterface $persistProcessor,
        private readonly FactoryInterface $bannerTranslationFactory,
        private readonly IriConverterInterface $iriConverter,
        private readonly TranslationLocaleProviderInterface $localeProvider,
        private readonly BannerStorer $bannerStorer,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BannerInterface
    {
        $request = $context['request'] ?? null;
        Assert::isInstanceOf($request, Request::class);

        $banner = $data;
        Assert::isInstanceOf($banner, BannerInterface::class);

        $banner->setCode($this->text($request->request->get('code')));
        $banner->setPosition($request->request->getInt('position'));
        $banner->setEnabled($request->request->getBoolean('enabled', true));

        foreach ($this->list($request, 'channels') as $iri) {
            $channel = $this->resource($iri);
            if (!$channel instanceof ChannelInterface) {
                throw new BadRequestHttpException(sprintf('"%s" is not a channel.', $iri));
            }

            $banner->addChannel($channel);
        }

        foreach ($this->list($request, 'taxons') as $iri) {
            $taxon = $this->resource($iri);
            if (!$taxon instanceof TaxonInterface) {
                throw new BadRequestHttpException(sprintf('"%s" is not a taxon.', $iri));
            }

            $banner->addTaxon($taxon);
        }

        $this->addTranslations($banner, $request);
        $this->bannerStorer->store($banner);

        return $this->persistProcessor->process($banner, $operation, $uriVariables, $context);
    }

    private function addTranslations(BannerInterface $banner, Request $request): void
    {
        $fields = $request->request->all()['translations'] ?? [];
        $files = $request->files->all()['translations'] ?? [];
        if (!is_array($fields) || !is_array($files)) {
            throw new BadRequestHttpException('The translations have to be keyed by locale.');
        }

        $locales = array_unique(array_merge(array_keys($fields), array_keys($files)));
        if ([] === $locales) {
            // The image of the missing translation is what gets reported.
            $locales = [$this->localeProvider->getDefaultLocaleCode()];
        }

        foreach ($locales as $locale) {
            $translationFields = is_array($fields[$locale] ?? null) ? $fields[$locale] : [];
            $translationFiles = is_array($files[$locale] ?? null) ? $files[$locale] : [];

            $translation = $this->bannerTranslationFactory->createNew();
            Assert::isInstanceOf($translation, BannerTranslationInterface::class);

            $translation->setLocale((string) $locale);
            $translation->setUrl($this->text($translationFields['url'] ?? null));
            $translation->setMainText($this->text($translationFields['mainText'] ?? null));
            $translation->setSecondaryText($this->text($translationFields['secondaryText'] ?? null));
            $translation->setButtonText($this->text($translationFields['buttonText'] ?? null));

            $image = $translationFiles['image'] ?? null;
            if ($image instanceof UploadedFile) {
                $translation->setImageFile($image);
            }

            $mobileImage = $translationFiles['mobileImage'] ?? null;
            if ($mobileImage instanceof UploadedFile) {
                $translation->setMobileImageFile($mobileImage);
            }

            $banner->addTranslation($translation);
        }
    }

    /** @return list<string> */
    private function list(Request $request, string $field): array
    {
        $values = $request->request->all()[$field] ?? [];
        if (!is_array($values)) {
            throw new BadRequestHttpException(sprintf('"%s" has to be a list of IRIs.', $field));
        }

        return array_values(array_filter($values, static fn (mixed $value): bool => is_string($value) && '' !== $value));
    }

    private function resource(string $iri): object
    {
        try {
            return $this->iriConverter->getResourceFromIri($iri);
        } catch (\Throwable $exception) {
            throw new BadRequestHttpException(sprintf('"%s" is not a valid IRI.', $iri), $exception);
        }
    }

    private function text(mixed $value): ?string
    {
        return is_string($value) && '' !== $value ? $value : null;
    }
}
