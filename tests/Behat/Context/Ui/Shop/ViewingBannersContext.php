<?php

declare(strict_types=1);

namespace Tests\Odiseo\SyliusBannerPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class ViewingBannersContext implements Context
{
    public function __construct(
        private readonly Session $session,
        private readonly HomePageInterface $homePage,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @When I visit the homepage
     */
    public function iVisitTheHomepage(): void
    {
        $this->homePage->open();
    }

    /**
     * @When /^I browse the products of the ("[^"]+" taxon)$/
     */
    public function iBrowseTheProductsOfTheTaxon(TaxonInterface $taxon): void
    {
        $this->session->visit($this->router->generate('sylius_shop_product_index', [
            'slug' => $taxon->getSlug(),
            '_locale' => 'en_US',
        ]));
    }

    /**
     * @Then I should see the banner :mainText
     */
    public function iShouldSeeTheBanner(string $mainText): void
    {
        Assert::inArray($mainText, $this->bannerTexts());
    }

    /**
     * @Then I should not see the banner :mainText
     */
    public function iShouldNotSeeTheBanner(string $mainText): void
    {
        Assert::false(in_array($mainText, $this->bannerTexts(), true));
    }

    /**
     * @Then I should not see any banner
     */
    public function iShouldNotSeeAnyBanner(): void
    {
        Assert::isEmpty($this->session->getPage()->findAll('css', '.banner-image-container'));
    }

    /**
     * @Then its image should be shown
     */
    public function itsImageShouldBeShown(): void
    {
        $image = $this->session->getPage()->find('css', '.banner-image-container img');

        Assert::notNull($image);
        Assert::contains((string) $image->getAttribute('src'), '/media/cache/resolve/odiseo_banner_image/');
    }

    /**
     * @Then I should see the banners :first and :second sliding in this order
     */
    public function iShouldSeeTheBannersSlidingInThisOrder(string $first, string $second): void
    {
        $slides = $this->session->getPage()->findAll('css', '#homepage-main-banner .swiper-slide .main-text');

        Assert::same(array_map(static fn (NodeElement $slide): string => $slide->getText(), $slides), [$first, $second]);
    }

    /** @return array<string> */
    private function bannerTexts(): array
    {
        return array_map(
            static fn (NodeElement $text): string => $text->getText(),
            $this->session->getPage()->findAll('css', '.banner-image-container .main-text'),
        );
    }
}
