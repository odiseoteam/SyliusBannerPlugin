@managing_banners
Feature: Deleting a banner
    In order to delete a banner
    As an Administrator
    I want to be able to delete a banner

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"
        And there is an existing banner with "banner_home" code

    @ui
    Scenario: Deleting a banner
        When I go to the banners page
        And I delete the banner "banner_home"
        Then I should be notified that it has been successfully deleted
