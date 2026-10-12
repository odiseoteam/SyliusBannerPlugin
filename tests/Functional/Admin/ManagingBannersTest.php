<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Functional\Admin;

use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\Channel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\DomCrawler\Form;
use Tests\Odiseo\SyliusBannerPlugin\Support\StoreTrait;

final class ManagingBannersTest extends WebTestCase
{
    use StoreTrait;

    private const IMAGE = __DIR__ . '/../../../config/app/fixtures/images/banner1.jpg';

    private KernelBrowser $browser;

    private Channel $channel;

    private FilesystemOperator $storage;

    protected function setUp(): void
    {
        $this->browser = self::createClient();
        $this->browser->disableReboot();

        $this->openStore();
        $this->channel = $this->createChannel('WEB', 'web.banner.test');

        $storage = self::getContainer()->get('odiseo_banner.storage.banner_image');
        self::assertInstanceOf(FilesystemOperator::class, $storage);
        $this->storage = $storage;

        $administrator = new AdminUser();
        $administrator->setUsername('banner_admin');
        $administrator->setEmail('banner_admin@example.com');
        $administrator->setPlainPassword('sylius');
        $administrator->setLocaleCode('en_US');
        $administrator->setEnabled(true);
        $this->entityManager->persist($administrator);
        $this->entityManager->flush();

        $this->browser->loginUser($administrator, 'admin');
    }

    protected function tearDown(): void
    {
        $this->removeBannerImages($this->storage);

        $this->closeStore();

        parent::tearDown();
    }

    public function testTheBannersAreListedInTheirOrder(): void
    {
        $this->createBanner('second', $this->channel, ['position' => 2]);
        $this->createBanner('first', $this->channel, ['position' => 1]);

        $crawler = $this->browser->request('GET', '/admin/banners/');

        self::assertResponseIsSuccessful();
        self::assertSame(
            ['first', 'second'],
            $crawler->filter('table tbody tr')->each(static fn (Crawler $row): string => $row->filter('td')->first()->text()),
        );
    }

    public function testTheListShowsWhereEachBannerIsShown(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('home_banner', $this->channel, ['position' => 1]);
        $this->createBanner('shirts_banner', $this->channel, ['position' => 2, 'taxon' => $shirts]);

        $crawler = $this->browser->request('GET', '/admin/banners/');

        self::assertResponseIsSuccessful();
        $rows = $crawler->filter('table tbody tr');
        self::assertStringContainsString('Homepage', $rows->eq(0)->text());
        self::assertStringContainsString('shirts', $rows->eq(1)->filter('.badge')->text());
    }

    public function testTheListIsFilteredByTaxon(): void
    {
        $shirts = $this->createTaxon('shirts');
        $hats = $this->createTaxon('hats');
        $this->createBanner('home_banner', $this->channel);
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts]);
        $this->createBanner('hats_banner', $this->channel, ['taxon' => $hats]);

        $crawler = $this->browser->request('GET', '/admin/banners/', ['criteria' => ['taxon' => $shirts->getId()]]);

        self::assertResponseIsSuccessful();
        self::assertSame(
            ['shirts_banner'],
            $crawler->filter('table tbody tr')->each(static fn (Crawler $row): string => $row->filter('td')->first()->text()),
        );
    }

    public function testTheFormIsSplitIntoItsCards(): void
    {
        $crawler = $this->browser->request('GET', '/admin/banners/new');

        self::assertResponseIsSuccessful();
        self::assertSame(
            ['Content', 'General', 'Visibility'],
            $crawler->filter('form .card > .card-header .card-title')->each(static fn (Crawler $title): string => $title->text()),
        );
        self::assertCount(1, $crawler->filter('#banner_code'));
        self::assertCount(1, $crawler->filter('#banner_taxons'));
        self::assertCount(1, $crawler->filter('#banner_translations_en_US_imageFile'));
    }

    public function testABannerIsAssignedToATaxon(): void
    {
        $shirts = $this->createTaxon('shirts');
        $banner = $this->createBanner('summer_sale', $this->channel);

        $crawler = $this->browser->request('GET', sprintf('/admin/banners/%d/edit', (int) $banner->getId()));

        $form = $crawler->selectButton('Update')->form();
        // The taxons are loaded while typing: none is among the options of the page.
        $form->disableValidation();
        $this->browser->submit($form, ['banner[taxons]' => [(string) $shirts->getCode()]]);

        self::assertResponseRedirects();
        self::assertSame(
            ['shirts'],
            $this->banner('summer_sale')->getTaxons()->map(static fn ($taxon): ?string => $taxon->getCode())->toArray(),
        );
    }

    public function testTheBannersAreReachableFromTheCatalogMenu(): void
    {
        $crawler = $this->browser->request('GET', '/admin/');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('a[href$="/admin/banners/"]'));
    }

    public function testCreatingABannerStoresItsImage(): void
    {
        $crawler = $this->browser->request('GET', '/admin/banners/new');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Create')->form();
        $form['banner[code]'] = 'summer_sale';
        $form['banner[position]'] = '3';
        $this->tickChannel($form, 'WEB');
        $this->attachImage($form);
        $form['banner[translations][en_US][mainText]'] = 'Summer sale';
        $form['banner[translations][en_US][url]'] = 'https://example.com/sale';
        $this->browser->submit($form);

        self::assertResponseRedirects();

        $banner = $this->banner('summer_sale');
        self::assertSame(3, $banner->getPosition());
        self::assertSame(['WEB'], $banner->getChannels()->map(static fn ($channel): ?string => $channel->getCode())->toArray());
        self::assertSame('Summer sale', $banner->getMainText());
        self::assertSame('https://example.com/sale', $banner->getUrl());

        $imageName = $banner->getImageName();
        self::assertNotNull($imageName);
        self::assertSame(file_get_contents(self::IMAGE), $this->storage->read($imageName));
    }

    public function testABannerCannotBeCreatedWithoutAnImage(): void
    {
        $crawler = $this->browser->request('GET', '/admin/banners/new');

        $form = $crawler->selectButton('Create')->form();
        $form['banner[code]'] = 'summer_sale';
        $this->browser->submit($form);

        self::assertResponseStatusCodeSame(422);
        self::assertNull($this->entityManager->getRepository(Banner::class)->findOneBy(['code' => 'summer_sale']));
    }

    public function testABannerCannotReuseTheCodeOfAnotherOne(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $crawler = $this->browser->request('GET', '/admin/banners/new');

        $form = $crawler->selectButton('Create')->form();
        $form['banner[code]'] = 'summer_sale';
        $this->attachImage($form);
        $this->browser->submit($form);

        self::assertResponseStatusCodeSame(422);
        self::assertCount(1, $this->entityManager->getRepository(Banner::class)->findBy(['code' => 'summer_sale']));
    }

    /** Losing the image on an edit that uploads none is the bug fixed in 2.1.1. */
    public function testEditingABannerWithoutUploadingAnImageKeepsTheStoredOne(): void
    {
        $this->storage->write('summer_sale.png', 'stored');
        $banner = $this->createBanner('summer_sale', $this->channel, ['main_text' => 'Summer sale']);

        $crawler = $this->browser->request('GET', sprintf('/admin/banners/%d/edit', (int) $banner->getId()));
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Update')->form();
        $form['banner[translations][en_US][mainText]'] = 'Winter sale';
        $this->browser->submit($form);

        self::assertResponseRedirects();

        $banner = $this->banner('summer_sale');
        self::assertSame('Winter sale', $banner->getMainText());
        self::assertSame('summer_sale.png', $banner->getImageName());
        self::assertSame('stored', $this->storage->read('summer_sale.png'));
    }

    public function testUploadingANewImageReplacesTheStoredOne(): void
    {
        $this->storage->write('summer_sale.png', 'stored');
        $banner = $this->createBanner('summer_sale', $this->channel);

        $crawler = $this->browser->request('GET', sprintf('/admin/banners/%d/edit', (int) $banner->getId()));

        $form = $crawler->selectButton('Update')->form();
        $this->attachImage($form);
        $this->browser->submit($form);

        self::assertResponseRedirects();

        $imageName = $this->banner('summer_sale')->getImageName();
        self::assertNotNull($imageName);
        self::assertNotSame('summer_sale.png', $imageName);
        self::assertFalse($this->storage->fileExists('summer_sale.png'));
        self::assertTrue($this->storage->fileExists($imageName));
    }

    public function testTheCodeOfABannerCannotBeChanged(): void
    {
        $banner = $this->createBanner('summer_sale', $this->channel);

        $crawler = $this->browser->request('GET', sprintf('/admin/banners/%d/edit', (int) $banner->getId()));

        self::assertNotNull($crawler->filter('#banner_code')->attr('disabled'));
    }

    public function testDeletingABanner(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $crawler = $this->browser->request('GET', '/admin/banners/');
        $this->browser->submit($crawler->filter('table tbody tr form')->form());

        self::assertResponseRedirects();
        $this->entityManager->clear();
        self::assertNull($this->entityManager->getRepository(Banner::class)->findOneBy(['code' => 'summer_sale']));
    }

    private function attachImage(Form $form): void
    {
        $field = $form->get('banner[translations][en_US][imageFile]');
        self::assertInstanceOf(FileFormField::class, $field);

        $field->upload(self::IMAGE);
    }

    private function tickChannel(Form $form, string $code): void
    {
        $channels = $form->get('banner[channels]');
        self::assertIsArray($channels);

        foreach ($channels as $channel) {
            self::assertInstanceOf(ChoiceFormField::class, $channel);
            if (in_array($code, $channel->availableOptionValues(), true)) {
                $channel->tick();

                return;
            }
        }

        self::fail(sprintf('There is no "%s" channel to tick.', $code));
    }

    private function banner(string $code): Banner
    {
        $this->entityManager->clear();

        $banner = $this->entityManager->getRepository(Banner::class)->findOneBy(['code' => $code]);
        self::assertInstanceOf(Banner::class, $banner);
        $banner->setCurrentLocale('en_US');
        $banner->setFallbackLocale('en_US');

        return $banner;
    }
}
