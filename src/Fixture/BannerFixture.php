<?php

namespace Odiseo\SyliusBannerPlugin\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BannerFixture extends AbstractFixture
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var FactoryInterface
     */
    protected $bannerFactory;

    /**
     * @var RepositoryInterface
     */
    protected $bannerRepository;

    /**
     * @var ChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @var RepositoryInterface
     */
    protected $localeRepository;

    /**
     * @var FactoryInterface
     */
    protected $taxonFactory;

    /**
     * @var TaxonRepositoryInterface
     */
    protected $taxonRepository;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var OptionsResolver
     */
    protected $optionsResolver;

    /**
     * @param ObjectManager $objectManager
     * @param FactoryInterface $bannerFactory
     * @param RepositoryInterface $bannerRepository
     * @param ChannelRepositoryInterface $channelRepository
     * @param RepositoryInterface $localeRepository
     * @param FactoryInterface $taxonFactory
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        FactoryInterface $bannerFactory,
        RepositoryInterface $bannerRepository,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository,
        FactoryInterface $taxonFactory,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $this->objectManager = $objectManager;
        $this->bannerFactory = $bannerFactory;
        $this->bannerRepository = $bannerRepository;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;
        $this->taxonFactory = $taxonFactory;
        $this->taxonRepository = $taxonRepository;

        $this->faker = Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setRequired('banners_per_channel')
                ->setAllowedTypes('banners_per_channel', 'int')
        ;
    }

    /**
     * @inheritDoc
     */
    public function load(array $options): void
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var TaxonInterface $taxonBanners */
        $taxonBanners = $this->taxonFactory->createNew();
        $taxonBanners->setCode('banners');
        foreach ($this->getLocales() as $localeCode) {
            $taxonBanners->setCurrentLocale($localeCode);
            $taxonBanners->setFallbackLocale($localeCode);

            $taxonBanners->setName('Banners');
            $taxonBanners->setSlug('banners');
            $taxonBanners->setDescription($this->faker->text);
        }

        /** @var TaxonInterface $taxonHome */
        $taxonHome = $this->taxonFactory->createNew();
        $taxonHome->setCode('home');
        $taxonHome->setParent($taxonBanners);
        foreach ($this->getLocales() as $localeCode) {
            $taxonHome->setCurrentLocale($localeCode);
            $taxonHome->setFallbackLocale($localeCode);

            $taxonHome->setName('Home');
            $taxonHome->setSlug('banners/home');
            $taxonHome->setDescription($this->faker->text);
        }

        $this->objectManager->persist($taxonBanners);
        $this->objectManager->persist($taxonHome);

        $channels = $this->channelRepository->findAll();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            /** @var BannerInterface $banner */
            $banner = $this->bannerFactory->createNew();

            $banner->setCode($this->faker->slug);
            $banner->addChannel($channel);
            $banner->addTaxon($taxonHome);

            foreach ($this->getLocales() as $localeCode) {
                $banner->setCurrentLocale($localeCode);
                $banner->setFallbackLocale($localeCode);

                $banner->setUrl($this->faker->url);

                $imageFinder = new Finder();
                $imagesPath = __DIR__ . '/../Resources/fixtures/banner';

                foreach ($imageFinder->files()->in($imagesPath)->name('01.png') as $img) {
                    /** @var string $path */
                    $path = $img->getRealPath();
                    /** @var string $filename */
                    $filename = $img->getFilename();
                    $file = new UploadedFile($path, $filename);
                    $banner->setImageFile($file);
                }
            }

            $this->objectManager->persist($banner);
        }

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $imageIndex = 1;
            for ($i=1; $i <= $options['banners_per_channel']; $i++) {
                /** @var BannerInterface $banner */
                $banner = $this->bannerFactory->createNew();

                $banner->setCode($this->faker->slug);
                $banner->addChannel($channel);

                foreach ($this->getLocales() as $localeCode) {
                    $banner->setCurrentLocale($localeCode);
                    $banner->setFallbackLocale($localeCode);

                    $banner->setUrl($this->faker->url);

                    $imageFinder = new Finder();
                    $imagesPath = __DIR__ . '/../Resources/fixtures/banner';

                    foreach ($imageFinder->files()->in($imagesPath)->name('0'.$imageIndex.'.png') as $img) {
                        /** @var string $path */
                        $path = $img->getRealPath();
                        /** @var string $filename */
                        $filename = $img->getFilename();
                        $file = new UploadedFile($path, $filename);
                        $banner->setImageFile($file);
                    }
                    $imageIndex = $imageIndex>=4?1:$imageIndex+1;
                }

                $this->objectManager->persist($banner);
            }
        }

        $this->objectManager->flush();
    }

    private function getLocales()
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->integerNode('banners_per_channel')->isRequired()->min(1)->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'banner';
    }
}
