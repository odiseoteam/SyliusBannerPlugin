@viewing_banners
Feature: Viewing banners
    In order to find out what the shop is promoting
    As a Visitor
    I want to see the banners of the channel I am browsing

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Seeing the banner of the channel on the homepage
        Given there is a banner "summer_sale" with the main text "Summer sale"
        When I visit the homepage
        Then I should see the banner "Summer sale"
        And its image should be shown

    @ui
    Scenario: Seeing no banner when there is none
        When I visit the homepage
        Then I should not see any banner

    @ui
    Scenario: Not seeing a disabled banner
        Given there is a disabled banner "summer_sale" with the main text "Summer sale"
        When I visit the homepage
        Then I should not see any banner

    @ui
    Scenario: Not seeing the banners of another channel
        Given the store also operates on another channel named "Outlet" with hostname "outlet.example.com"
        And there is a banner "outlet_sale" with the main text "Outlet sale" in the "Outlet" channel
        And there is a banner "summer_sale" with the main text "Summer sale" in the "United States" channel
        When I browse the "United States" channel
        Then I should see the banner "Summer sale"
        But I should not see the banner "Outlet sale"

    @ui
    Scenario: Seeing several banners sliding in their order
        Given there is a banner "winter_sale" with the main text "Winter sale" at position 2
        And there is a banner "summer_sale" with the main text "Summer sale" at position 1
        When I visit the homepage
        Then I should see the banners "Summer sale" and "Winter sale" sliding in this order

    @ui
    Scenario: Seeing the banner of a taxon only on its page
        Given the store classifies its products as "T-Shirts" and "Caps"
        And there is a banner "summer_sale" with the main text "Summer sale"
        And there is a banner "t_shirts_banner" with the main text "Soft cotton tees" for the "T-Shirts" taxon
        When I browse the products of the "T-Shirts" taxon
        Then I should see the banner "Soft cotton tees"
        But I should not see the banner "Summer sale"

    @ui
    Scenario: Not seeing the banner of a taxon on the homepage
        Given the store classifies its products as "T-Shirts"
        And there is a banner "summer_sale" with the main text "Summer sale"
        And there is a banner "t_shirts_banner" with the main text "Soft cotton tees" for the "T-Shirts" taxon
        When I visit the homepage
        Then I should see the banner "Summer sale"
        But I should not see the banner "Soft cotton tees"

    @ui
    Scenario: Not seeing the banner of another taxon
        Given the store classifies its products as "T-Shirts" and "Caps"
        And there is a banner "t_shirts_banner" with the main text "Soft cotton tees" for the "T-Shirts" taxon
        When I browse the products of the "Caps" taxon
        Then I should not see any banner

    @ui
    Scenario: Seeing the banner of the parent taxon when a taxon has none
        Given the store classifies its products as "T-Shirts"
        And the "T-Shirts" taxon has child taxon "Men"
        And there is a banner "t_shirts_banner" with the main text "Soft cotton tees" for the "T-Shirts" taxon
        When I browse the products of the "Men" taxon
        Then I should see the banner "Soft cotton tees"
