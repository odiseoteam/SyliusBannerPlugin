@managing_banners
Feature: Editing a banner
    In order to change a banner
    As an Administrator
    I want to be able to edit a banner

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"
        And there is an existing banner with "banner_home" code

    @ui
    Scenario: Renaming a banner
        Given I want to modify the banner "banner_home"
        When I rename the url with "https://odiseo.com.ar"
        And I save my changes
        Then I should be notified that it has been successfully edited
