<?php

declare(strict_types=1);

namespace Odiseo\SyliusBannerPlugin\Fixture\Factory;

use Faker\Factory;
use Faker\Generator as FakerGenerator;
use Generator;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BannerExampleFactory extends AbstractExampleFactory
{
    protected FakerGenerator $faker;

    protected OptionsResolver $optionsResolver;

    public function __construct(
        protected FactoryInterface $bannerFactory,
        protected ChannelRepositoryInterface $channelRepository,
        protected TaxonRepositoryInterface $taxonRepository,
        protected RepositoryInterface $localeRepository,
        protected EventDispatcherInterface $eventDispatcher,
        protected ?FileLocatorInterface $fileLocator = null,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): BannerInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var BannerInterface $banner */
        $banner = $this->bannerFactory->createNew();
        $banner->setCode($options['code']);

        foreach ($options['channels'] as $channel) {
            $banner->addChannel($channel);
        }

        foreach ($options['taxons'] as $taxon) {
            $banner->addTaxon($taxon);
        }

        /** @var string $localeCode */
        foreach ($this->getLocales() as $localeCode) {
            $banner->setCurrentLocale($localeCode);
            $banner->setFallbackLocale($localeCode);

            $banner->setImageFile($this->createImage($options['image']));

            if ($options['main_text']) {
                $banner->setMainText($options['main_text']);
            }
            if ($options['url']) {
                $banner->setUrl($options['url']);
            }
            if ($options['secondary_text']) {
                $banner->setSecondaryText($options['secondary_text']);
            }
            if ($options['button_text']) {
                $banner->setButtonText($options['button_text']);
            }
            if ($options['mobile_image']) {
                $banner->setMobileImageFile($this->createImage($options['mobile_image']));
            }
        }

        $event = new ResourceControllerEvent($banner);
        $this->eventDispatcher->dispatch($event, 'odiseo_banner.banner.pre_create');

        return $banner;
    }

    protected function createImage(string $imagePath): UploadedFile
    {
        /**
         * @var string $imagePath
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $imagePath = null === $this->fileLocator ? $imagePath : $this->fileLocator->locate($imagePath);

        return new UploadedFile($imagePath, basename($imagePath));
    }

    protected function getLocales(): Generator
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('code')
            ->setDefault('code', function (Options $_options): string {
                return $this->faker->slug();
            })
            ->setAllowedTypes('code', ['string'])

            ->setDefault('main_text', null)
            ->setAllowedTypes('main_text', ['string', 'null'])

            ->setDefault('secondary_text', null)
            ->setAllowedTypes('secondary_text', ['string', 'null'])

            ->setDefault('button_text', null)
            ->setAllowedTypes('button_text', ['string', 'null'])

            ->setDefault('url', function (Options $_options): string {
                return $this->faker->url();
            })
            ->setAllowedTypes('url', ['string', 'null'])

            ->setDefault('image', function (Options $_options): string {
                return __DIR__ . '/../../../config/app/fixtures/images/banner1.png';
            })
            ->setAllowedTypes('image', ['string'])

            ->setDefault('mobile_image', function (Options $_options): string {
                return __DIR__ . '/../../../config/app/fixtures/images/banner1.png';
            })
            ->setAllowedTypes('mobile_image', ['string', 'null'])

            ->setDefault('channels', LazyOption::randomOnes($this->channelRepository, 3))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))

            ->setDefault('taxons', [])
            ->setAllowedTypes('taxons', 'array')
            ->setNormalizer('taxons', LazyOption::findBy($this->taxonRepository, 'code'))
        ;
    }
}
