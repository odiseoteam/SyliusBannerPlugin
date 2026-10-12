@managing_banners
Feature: Managing banners
    In order to promote what matters on my shop
    As an Administrator
    I want to manage the banners of each channel

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a banner
        When I want to create a new banner
        And I specify its code as "summer_sale"
        And I set its position to 2
        And I make it available in the "United States" channel
        And I attach the "ford.jpg" image to it
        And I set its main text to "Summer sale"
        And I add it
        Then I should be notified that it has been successfully created
        And the banner "summer_sale" should appear in the list
        And the banner "summer_sale" should be at position 2
        And the main text of the banner "summer_sale" should be "Summer sale"
        And the image of the banner "summer_sale" should be stored

    @ui
    Scenario: Trying to add a banner without an image
        When I want to create a new banner
        And I specify its code as "summer_sale"
        And I try to add it
        Then I should be notified that the image is required
        And there should be no banner "summer_sale"

    @ui
    Scenario: Trying to add a banner with a code that is already used
        Given there is a banner "summer_sale"
        When I want to create a new banner
        And I specify its code as "summer_sale"
        And I attach the "ford.jpg" image to it
        And I try to add it
        Then I should be notified that the code is already used
        And there should still be only one banner "summer_sale"

    @ui
    Scenario: Browsing banners in their order
        Given there is a banner "winter_sale" with the main text "Winter sale" at position 2
        And there is a banner "summer_sale" with the main text "Summer sale" at position 1
        When I browse banners
        Then I should see 2 banners in the list
        And I should see the banners "summer_sale" and "winter_sale" in this order

    @ui
    Scenario: Editing a banner keeps its image
        Given there is a banner "summer_sale" with the main text "Summer sale"
        When I want to modify the banner "summer_sale"
        And I set its main text to "Winter sale"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the main text of the banner "summer_sale" should be "Winter sale"
        And the banner "summer_sale" should still have its image

    @ui
    Scenario: Replacing the image of a banner
        Given there is a banner "summer_sale"
        When I want to modify the banner "summer_sale"
        And I attach the "ford.jpg" image to it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the image of the banner "summer_sale" should have been replaced

    @ui
    Scenario: Being unable to change the code of a banner
        Given there is a banner "summer_sale"
        When I want to modify the banner "summer_sale"
        Then I should not be able to edit its code

    @ui
    Scenario: Disabling a banner
        Given there is a banner "summer_sale"
        When I want to modify the banner "summer_sale"
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the banner "summer_sale" should be disabled

    @ui
    Scenario: Deleting a banner
        Given there is a banner "summer_sale"
        When I browse banners
        And I delete the banner "summer_sale"
        Then I should be notified that it has been successfully deleted
        And there should be no banner "summer_sale"
