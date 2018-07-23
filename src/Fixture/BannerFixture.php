<?php

namespace Odiseo\SyliusBannerPlugin\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Odiseo\SyliusBannerPlugin\Model\BannerInterface;
use Odiseo\SyliusBannerPlugin\Model\ChannelInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
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
     */
    public function __construct(
        ObjectManager $objectManager,
        FactoryInterface $bannerFactory,
        RepositoryInterface $bannerRepository,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $localeRepository
    )
    {
        $this->objectManager = $objectManager;
        $this->bannerFactory = $bannerFactory;
        $this->bannerRepository = $bannerRepository;
        $this->channelRepository = $channelRepository;
        $this->localeRepository = $localeRepository;

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

        $channels = $this->channelRepository->findAll();

        /** @var ChannelInterface $channel */
        foreach($channels as $channel)
        {
            $imageIndex = 1;
            for($i=1; $i <= $options['banners_per_channel']; $i++)
            {
                /** @var BannerInterface $banner */
                $banner = $this->bannerFactory->createNew();

                $banner->setCode($this->faker->company);
                $banner->addChannel($channel);

                foreach ($this->getLocales() as $localeCode) {
                    $banner->setCurrentLocale($localeCode);
                    $banner->setFallbackLocale($localeCode);

                    $banner->setUrl($this->faker->url);

                    $imageFinder = new Finder();
                    $imagesPath = __DIR__ . '/../Resources/fixtures/banner';

                    foreach ($imageFinder->files()->in($imagesPath)->name('0'.$imageIndex.'.png') as $img)
                    {
                        $file = new UploadedFile($img->getRealPath(), $img->getFilename());
                        $banner->setImageFile($file);
                    }
                    $imageIndex = $imageIndex>=4?1:$imageIndex+1;
                }

                $this->objectManager->persist($banner);
            }
        }

        $this->objectManager->flush();
    }

    /**
     * @return array
     */
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
