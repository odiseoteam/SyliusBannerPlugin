<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Odiseo\SyliusBannerPlugin\Entity\BannerInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner\CreatePageInterface;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner\IndexPageInterface;
use Tests\Odiseo\SyliusBannerPlugin\Behat\Page\Admin\Banner\UpdatePageInterface;
use Webmozart\Assert\Assert;

final class ManagingBannersContext implements Context
{
    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var IndexPageInterface */
    private $indexPage;

    /** @var CreatePageInterface */
    private $createPage;

    /** @var UpdatePageInterface */
    private $updatePage;

    public function __construct(
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage
    ) {
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given I want to add a new banner
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function iWantToAddNewBanner(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I fill the code with :bannerCode
     * @param $bannerCode
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheCodeWith($bannerCode): void
    {
        $this->createPage->fillCode($bannerCode);
    }

    /**
     * @When I fill the url with :bannerUrl
     * @param $bannerUrl
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iFillTheUrlWith($bannerUrl): void
    {
        $this->createPage->fillUrl($bannerUrl);
    }

    /**
     * @When I upload the :file image
     * @param $bannerImage
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iUploadTheImage($bannerImage): void
    {
        $this->resolveCurrentPage()->uploadFile($bannerImage, 'Image');
    }

    /**
     * @When I upload the :file mobile image
     * @param $bannerImage
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iUploadTheMobileImage($bannerImage): void
    {
        $this->resolveCurrentPage()->uploadFile($bannerImage, 'Mobile image');
    }

    /**
     * @When I add it
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @Then /^the (banner "([^"]+)") should appear in the admin/
     * @param BannerInterface $banner
     * @throws \FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException
     */
    public function bannerShouldAppearInTheAdmin(BannerInterface $banner): void
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $banner->getCode()]),
            sprintf('Banner %s should exist but it does not', $banner->getCode())
        );
    }

    /**
     * @Then I should be notified that the form contains invalid fields
     */
    public function iShouldBeNotifiedThatTheFormContainsInvalidFields(): void
    {
        Assert::true($this->resolveCurrentPage()->containsError(),
            sprintf('The form should be notified you that the form contains invalid field but it does not')
        );
    }

    /**
     * @Then I should be notified that there is already an existing banner with provided code
     */
    public function iShouldBeNotifiedThatThereIsAlreadyAnExistingBannerWithCode(): void
    {
        Assert::true($this->resolveCurrentPage()->containsErrorWithMessage(
            'There is an existing banner with this code.',
            false
        ));
    }

    /**
     * @return IndexPageInterface|CreatePageInterface|UpdatePageInterface|SymfonyPageInterface
     */
    private function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->indexPage,
            $this->createPage,
            $this->updatePage
        ]);
    }
}
