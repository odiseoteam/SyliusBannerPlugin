@managing_banners
Feature: Adding a new banner
    In order to show different images
    As an Administrator
    I want to add a new banner to the admin

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"

    @ui
    Scenario: Trying to add a new banner with empty fields
        Given I want to add a new banner
        When I fill the code with "banner_home"
        And I add it
        Then I should be notified that the form contains invalid fields

    @ui
    Scenario: Adding a new banner
        Given I want to add a new banner
        When I fill the code with "banner_home"
        And I fill the url with "https://odiseo.com.ar"
        And I upload the "logo_odiseo.png" image
        And I add it
        Then I should be notified that it has been successfully created
        And the banner "banner_home" should appear in the admin

    @ui
    Scenario: Trying to add a banner with existing code
        Given there is an existing banner with "banner_home" code
        And I want to add a new banner
        When I fill the code with "banner_home"
        And I add it
        Then I should be notified that there is already an existing banner with provided code
