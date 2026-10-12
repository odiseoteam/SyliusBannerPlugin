<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Functional\Api;

use League\Flysystem\FilesystemOperator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Odiseo\SyliusBannerPlugin\Entity\Banner;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\Channel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Odiseo\SyliusBannerPlugin\Support\StoreTrait;

final class AdminBannersTest extends WebTestCase
{
    use StoreTrait;

    private const IMAGE = __DIR__ . '/../../../config/app/fixtures/images/banner1.jpg';

    private KernelBrowser $browser;

    private Channel $channel;

    private FilesystemOperator $storage;

    private string $token;

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
        $administrator->setUsername('banner_api_admin');
        $administrator->setEmail('banner_api_admin@example.com');
        $administrator->setPlainPassword('sylius');
        $administrator->setLocaleCode('en_US');
        $administrator->setEnabled(true);
        $administrator->addRole('ROLE_API_ACCESS');
        $this->entityManager->persist($administrator);
        $this->entityManager->flush();

        $tokenManager = self::getContainer()->get('lexik_jwt_authentication.jwt_manager');
        self::assertInstanceOf(JWTTokenManagerInterface::class, $tokenManager);
        $this->token = $tokenManager->create($administrator);
    }

    protected function tearDown(): void
    {
        $this->removeBannerImages($this->storage);
        $this->closeStore();

        parent::tearDown();
    }

    public function testItRequiresAnAdministrator(): void
    {
        $this->browser->request('GET', '/api/v2/admin/banners', server: ['HTTP_ACCEPT' => 'application/json']);

        self::assertResponseStatusCodeSame(401);
    }

    public function testItListsTheBannersInTheirOrder(): void
    {
        $this->createBanner('second', $this->channel, ['position' => 2]);
        $this->createBanner('first', $this->channel, ['position' => 1]);
        $this->createBanner('disabled', $this->channel, ['position' => 3, 'enabled' => false]);

        $banners = $this->get('/api/v2/admin/banners');

        self::assertResponseIsSuccessful();
        self::assertSame(['first', 'second', 'disabled'], array_column($banners, 'code'));
    }

    public function testItFiltersTheBannersByStateAndCode(): void
    {
        $this->createBanner('summer_sale', $this->channel);
        $this->createBanner('winter_sale', $this->channel, ['enabled' => false]);

        self::assertSame(['winter_sale'], array_column($this->get('/api/v2/admin/banners?enabled=false'), 'code'));
        self::assertSame(['summer_sale'], array_column($this->get('/api/v2/admin/banners?code=summer'), 'code'));
    }

    public function testItFiltersTheBannersByTaxon(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('home_banner', $this->channel);
        $this->createBanner('shirts_banner', $this->channel, ['taxon' => $shirts]);

        self::assertSame(['shirts_banner'], array_column($this->get('/api/v2/admin/banners?taxons.code=shirts'), 'code'));
    }

    public function testItShowsABannerWithItsTranslationsAndImages(): void
    {
        $shirts = $this->createTaxon('shirts');
        $this->createBanner('summer_sale', $this->channel, [
            'position' => 4,
            'taxon' => $shirts,
            'main_text' => 'Summer sale',
            'url' => 'https://example.com/sale',
            'mobile_image' => 'summer_sale_mobile.png',
        ]);

        $banner = $this->get('/api/v2/admin/banners/summer_sale');

        self::assertResponseIsSuccessful();
        self::assertSame('summer_sale', $banner['code']);
        self::assertSame(4, $banner['position']);
        self::assertTrue($banner['enabled']);
        self::assertSame(['/api/v2/admin/channels/WEB'], $banner['channels']);
        self::assertSame(['/api/v2/admin/taxons/shirts'], $banner['taxons']);

        $translation = $this->translation($banner, 'en_US');
        self::assertSame('Summer sale', $translation['mainText']);
        self::assertSame('https://example.com/sale', $translation['url']);
        self::assertSame('summer_sale.png', $translation['imageName']);
        self::assertIsString($translation['image']);
        self::assertStringContainsString('/media/cache/resolve/odiseo_banner_image/summer_sale.png', $translation['image']);
        self::assertIsString($translation['mobileImage']);
        self::assertStringContainsString('/media/cache/resolve/odiseo_banner_mobile_image/summer_sale_mobile.png', $translation['mobileImage']);
    }

    public function testItCreatesABannerWithItsImages(): void
    {
        $shirts = $this->createTaxon('shirts');

        $banner = $this->post('/api/v2/admin/banners', [
            'code' => 'summer_sale',
            'position' => '3',
            'enabled' => '0',
            'channels' => ['/api/v2/admin/channels/WEB'],
            'taxons' => ['/api/v2/admin/taxons/' . $shirts->getCode()],
            'translations' => ['en_US' => [
                'url' => 'https://example.com/sale',
                'mainText' => 'Summer sale',
                'secondaryText' => 'Up to 50% off',
                'buttonText' => 'Shop now',
            ]],
        ], ['translations' => ['en_US' => ['image' => $this->image(), 'mobileImage' => $this->image()]]]);

        self::assertResponseStatusCodeSame(201);
        self::assertSame('summer_sale', $banner['code']);
        self::assertSame(3, $banner['position']);
        self::assertFalse($banner['enabled']);
        self::assertSame(['/api/v2/admin/channels/WEB'], $banner['channels']);
        self::assertSame(['/api/v2/admin/taxons/shirts'], $banner['taxons']);

        $translation = $this->translation($banner, 'en_US');
        self::assertSame('Summer sale', $translation['mainText']);
        self::assertSame('Up to 50% off', $translation['secondaryText']);
        self::assertSame('Shop now', $translation['buttonText']);
        self::assertSame('https://example.com/sale', $translation['url']);

        self::assertIsString($translation['imageName']);
        self::assertIsString($translation['mobileImageName']);
        self::assertSame(file_get_contents(self::IMAGE), $this->storage->read($translation['imageName']));
        self::assertTrue($this->storage->fileExists($translation['mobileImageName']));

        self::assertInstanceOf(Banner::class, $this->entityManager->getRepository(Banner::class)->findOneBy(['code' => 'summer_sale']));
    }

    public function testItDoesNotCreateABannerWithoutAnImage(): void
    {
        $problem = $this->post('/api/v2/admin/banners', ['code' => 'summer_sale']);

        self::assertResponseStatusCodeSame(422);
        self::assertSame(['translations[en_US].imageFile'], $this->violatedPaths($problem));
        self::assertNull($this->entityManager->getRepository(Banner::class)->findOneBy(['code' => 'summer_sale']));
    }

    public function testItDoesNotCreateABannerWithACodeAlreadyUsed(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $problem = $this->post(
            '/api/v2/admin/banners',
            ['code' => 'summer_sale'],
            ['translations' => ['en_US' => ['image' => $this->image()]]],
        );

        self::assertResponseStatusCodeSame(422);
        self::assertSame(['code'], $this->violatedPaths($problem));
    }

    public function testItOnlyAcceptsImages(): void
    {
        $problem = $this->post(
            '/api/v2/admin/banners',
            ['code' => 'summer_sale'],
            ['translations' => ['en_US' => ['image' => new UploadedFile(__FILE__, 'banner.php', 'text/x-php', null, true)]]],
        );

        self::assertResponseStatusCodeSame(422);
        self::assertSame(['translations[en_US].imageFile'], $this->violatedPaths($problem));
        self::assertSame([], $this->storage->listContents('')->filter(
            static fn ($file): bool => str_ends_with($file->path(), '.php'),
        )->toArray());
    }

    public function testItRejectsAChannelThatDoesNotExist(): void
    {
        $this->post(
            '/api/v2/admin/banners',
            ['code' => 'summer_sale', 'channels' => ['/api/v2/admin/channels/NOPE']],
            ['translations' => ['en_US' => ['image' => $this->image()]]],
        );

        self::assertResponseStatusCodeSame(400);
    }

    public function testItUpdatesABannerKeepingItsImages(): void
    {
        $outlet = $this->createChannel('OUTLET', 'outlet.banner.test');
        $this->createBanner('summer_sale', $this->channel, ['main_text' => 'Summer sale', 'url' => 'https://example.com/sale']);

        $banner = $this->put('/api/v2/admin/banners/summer_sale', [
            'position' => 7,
            'enabled' => false,
            'channels' => ['/api/v2/admin/channels/' . $outlet->getCode()],
            'translations' => ['en_US' => ['mainText' => 'Winter sale', 'buttonText' => 'Shop now']],
        ]);

        self::assertResponseIsSuccessful();
        self::assertSame(7, $banner['position']);
        self::assertFalse($banner['enabled']);
        self::assertSame(['/api/v2/admin/channels/OUTLET'], $banner['channels']);

        $translation = $this->translation($banner, 'en_US');
        self::assertSame('Winter sale', $translation['mainText']);
        self::assertSame('Shop now', $translation['buttonText']);
        self::assertSame('https://example.com/sale', $translation['url']);
        self::assertSame('summer_sale.png', $translation['imageName']);
    }

    public function testItDoesNotChangeTheCodeOfABanner(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $banner = $this->put('/api/v2/admin/banners/summer_sale', ['code' => 'winter_sale']);

        self::assertResponseIsSuccessful();
        self::assertSame('summer_sale', $banner['code']);
    }

    public function testItDoesNotAddATranslationWithoutAnImage(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $problem = $this->put('/api/v2/admin/banners/summer_sale', [
            'translations' => ['es_ES' => ['mainText' => 'Rebajas de verano']],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertSame(['translations[es_ES].imageName'], $this->violatedPaths($problem));
    }

    public function testItReplacesTheImageOfATranslation(): void
    {
        $this->storage->write('summer_sale.png', 'stored');
        $this->createBanner('summer_sale', $this->channel, ['main_text' => 'Summer sale']);

        $banner = $this->post('/api/v2/admin/banners/summer_sale/images', ['locale' => 'en_US'], ['image' => $this->image()]);

        self::assertResponseStatusCodeSame(201);
        $translation = $this->translation($banner, 'en_US');
        self::assertSame('Summer sale', $translation['mainText']);
        self::assertIsString($translation['imageName']);
        self::assertNotSame('summer_sale.png', $translation['imageName']);
        self::assertFalse($this->storage->fileExists('summer_sale.png'));
        self::assertTrue($this->storage->fileExists($translation['imageName']));
    }

    public function testItAddsAMobileImageKeepingTheMainOne(): void
    {
        $this->storage->write('summer_sale.png', 'stored');
        $this->createBanner('summer_sale', $this->channel);

        $banner = $this->post('/api/v2/admin/banners/summer_sale/images', ['locale' => 'en_US'], ['mobileImage' => $this->image()]);

        self::assertResponseStatusCodeSame(201);
        $translation = $this->translation($banner, 'en_US');
        self::assertSame('summer_sale.png', $translation['imageName']);
        self::assertIsString($translation['mobileImageName']);
        self::assertTrue($this->storage->fileExists($translation['mobileImageName']));
        self::assertSame('stored', $this->storage->read('summer_sale.png'));
    }

    public function testItAddsATranslationThroughItsImage(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $banner = $this->post('/api/v2/admin/banners/summer_sale/images', ['locale' => 'es_ES'], ['image' => $this->image()]);

        self::assertResponseStatusCodeSame(201);
        self::assertIsString($this->translation($banner, 'es_ES')['imageName']);
        self::assertSame('summer_sale.png', $this->translation($banner, 'en_US')['imageName']);
    }

    public function testUploadingImagesNeedsALocaleAndAFile(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $this->post('/api/v2/admin/banners/summer_sale/images', [], ['image' => $this->image()]);
        self::assertResponseStatusCodeSame(400);

        $this->post('/api/v2/admin/banners/summer_sale/images', ['locale' => 'en_US']);
        self::assertResponseStatusCodeSame(400);
    }

    public function testUploadingImagesToAMissingBannerIsNotFound(): void
    {
        $this->post('/api/v2/admin/banners/missing/images', ['locale' => 'en_US'], ['image' => $this->image()]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testItDeletesABanner(): void
    {
        $this->createBanner('summer_sale', $this->channel);

        $this->browser->request('DELETE', '/api/v2/admin/banners/summer_sale', server: $this->headers());

        self::assertResponseStatusCodeSame(204);
        $this->entityManager->clear();
        self::assertNull($this->entityManager->getRepository(Banner::class)->findOneBy(['code' => 'summer_sale']));
    }

    private function image(): UploadedFile
    {
        return new UploadedFile(self::IMAGE, 'banner.jpg', 'image/jpeg', null, true);
    }

    /** @return array<mixed> */
    private function get(string $uri): array
    {
        $this->browser->request('GET', $uri, server: $this->headers());

        return $this->decodedResponse();
    }

    /**
     * @param array<string, mixed> $fields
     * @param array<string, mixed> $files
     *
     * @return array<mixed>
     */
    private function post(string $uri, array $fields = [], array $files = []): array
    {
        $this->browser->request('POST', $uri, $fields, $files, $this->headers() + ['CONTENT_TYPE' => 'multipart/form-data']);

        return $this->decodedResponse();
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<mixed>
     */
    private function put(string $uri, array $payload): array
    {
        $this->browser->request(
            'PUT',
            $uri,
            server: $this->headers() + ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        return $this->decodedResponse();
    }

    /** @return array<string, string> */
    private function headers(): array
    {
        return ['HTTP_ACCEPT' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token];
    }

    /** @return array<mixed> */
    private function decodedResponse(): array
    {
        $content = (string) $this->browser->getResponse()->getContent();
        if ('' === $content) {
            return [];
        }

        $decoded = json_decode($content, true, flags: \JSON_THROW_ON_ERROR);
        self::assertIsArray($decoded);

        return $decoded;
    }

    /**
     * @param array<mixed> $banner
     *
     * @return array<mixed>
     */
    private function translation(array $banner, string $locale): array
    {
        self::assertIsArray($banner['translations'] ?? null);
        $translation = $banner['translations'][$locale] ?? null;
        self::assertIsArray($translation);

        return $translation;
    }

    /**
     * @param array<mixed> $problem
     *
     * @return list<string>
     */
    private function violatedPaths(array $problem): array
    {
        self::assertIsArray($problem['violations'] ?? null);

        $paths = [];
        foreach ($problem['violations'] as $violation) {
            self::assertIsArray($violation);
            self::assertIsString($violation['propertyPath'] ?? null);
            $paths[$violation['propertyPath']] = true;
        }

        return array_keys($paths);
    }
}
