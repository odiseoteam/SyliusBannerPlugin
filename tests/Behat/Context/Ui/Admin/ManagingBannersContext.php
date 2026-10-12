<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Odiseo\SyliusBannerPlugin\Repository\BannerRepositoryInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Setup\BannerContext;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner\CreatePageInterface;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingBannersContext implements Context
{
    private const LOCALE = 'en_US';

    public function __construct(
        private readonly CurrentPageResolverInterface $currentPageResolver,
        private readonly IndexPageInterface $indexPage,
        private readonly CreatePageInterface $createPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly BannerRepositoryInterface $bannerRepository,
        private readonly ObjectManager $bannerManager,
        private readonly FilesystemOperator $storage,
    ) {
    }

    /**
     * @When I browse banners
     */
    public function iBrowseBanners(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I want to create a new banner
     */
    public function iWantToCreateANewBanner(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I want to modify the banner :code
     */
    public function iWantToModifyTheBanner(string $code): void
    {
        $this->updatePage->open(['id' => $this->banner($code)->getId()]);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I set its position to :position
     */
    public function iSetItsPositionTo(int $position): void
    {
        $this->resolveCurrentPage()->specifyPosition($position);
    }

    /**
     * @When I make it available in the :name channel
     */
    public function iMakeItAvailableInTheChannel(string $name): void
    {
        $this->resolveCurrentPage()->checkChannel($name);
    }

    /**
     * @When I attach the :path image to it
     */
    public function iAttachTheImageToIt(string $path): void
    {
        $this->resolveCurrentPage()->attachImage($path, self::LOCALE);
    }

    /**
     * @When I set its main text to :mainText
     */
    public function iSetItsMainTextTo(string $mainText): void
    {
        $this->resolveCurrentPage()->specifyMainText($mainText, self::LOCALE);
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->resolveCurrentPage()->disable();
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the banner :code
     */
    public function iDeleteTheBanner(string $code): void
    {
        $this->indexPage->deleteResourceOnPage(['code' => $code]);
    }

    /**
     * @Then I should see :count banner(s) in the list
     */
    public function iShouldSeeBannersInTheList(int $count): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then the banner :code should appear in the list
     */
    public function theBannerShouldAppearInTheList(string $code): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then there should be no banner :code
     */
    public function thereShouldBeNoBanner(string $code): void
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then there should still be only one banner :code
     */
    public function thereShouldStillBeOnlyOneBanner(string $code): void
    {
        Assert::count($this->bannerRepository->findBy(['code' => $code]), 1);
    }

    /**
     * @Then I should see the banners :first and :second in this order
     */
    public function iShouldSeeTheBannersInThisOrder(string $first, string $second): void
    {
        Assert::same($this->indexPage->getColumnFields('code'), [$first, $second]);
    }

    /**
     * @Then I should be notified that the image is required
     */
    public function iShouldBeNotifiedThatTheImageIsRequired(): void
    {
        Assert::same($this->createPage->getErrorOf('image', self::LOCALE), 'Image cannot be blank.');
    }

    /**
     * @Then I should be notified that the code is already used
     */
    public function iShouldBeNotifiedThatTheCodeIsAlreadyUsed(): void
    {
        Assert::same($this->createPage->getErrorOf('code'), 'There is an existing banner with this code.');
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then the main text of the banner :code should be :mainText
     */
    public function theMainTextOfTheBannerShouldBe(string $code, string $mainText): void
    {
        Assert::same($this->banner($code)->getMainText(), $mainText);
    }

    /**
     * @Then the banner :code should be at position :position
     */
    public function theBannerShouldBeAtPosition(string $code, int $position): void
    {
        Assert::same($this->banner($code)->getPosition(), $position);
    }

    /**
     * @Then the banner :code should be disabled
     */
    public function theBannerShouldBeDisabled(string $code): void
    {
        Assert::false($this->banner($code)->isEnabled());
    }

    /**
     * @Then the image of the banner :code should be stored
     */
    public function theImageOfTheBannerShouldBeStored(string $code): void
    {
        $imageName = $this->banner($code)->getImageName();

        Assert::notNull($imageName);
        Assert::true($this->storage->fileExists($imageName));
    }

    /**
     * @Then the banner :code should still have its image
     */
    public function theBannerShouldStillHaveItsImage(string $code): void
    {
        $imageName = $this->banner($code)->getImageName();

        Assert::same($imageName, $code . '.png');
        Assert::same($this->storage->read($code . '.png'), BannerContext::STORED_IMAGE);
    }

    /**
     * @Then the image of the banner :code should have been replaced
     */
    public function theImageOfTheBannerShouldHaveBeenReplaced(string $code): void
    {
        $imageName = $this->banner($code)->getImageName();

        Assert::notNull($imageName);
        Assert::notSame($imageName, $code . '.png');
        Assert::true($this->storage->fileExists($imageName));
        Assert::false($this->storage->fileExists($code . '.png'));
    }

    private function banner(string $code): BannerInterface
    {
        $this->bannerManager->clear();

        $banner = $this->bannerRepository->findOneBy(['code' => $code]);
        Assert::isInstanceOf($banner, BannerInterface::class);
        $banner->setCurrentLocale(self::LOCALE);
        $banner->setFallbackLocale(self::LOCALE);

        return $banner;
    }

    private function resolveCurrentPage(): CreatePageInterface|UpdatePageInterface
    {
        /** @var CreatePageInterface|UpdatePageInterface $page */
        $page = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        return $page;
    }
}
