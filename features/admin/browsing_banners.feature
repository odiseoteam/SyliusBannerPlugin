@managing_banners
Feature: Browsing banners
    In order to show different banners
    As an Administrator
    I want to be able to browse banners

    Background:
        Given I am logged in as an administrator
        And the store operates on a single channel in "United States"
        And the store has 5 banners

    @ui
    Scenario: Browsing defined banners
        When I want to browse banners
        Then I should see 5 banners in the list
