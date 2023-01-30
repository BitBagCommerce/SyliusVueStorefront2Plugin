@wishlist_as_customer
Feature: Fetching wishlists
    In order to fetch all available wishlists
    As a customer
    I want to be able to fetch list of my wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For me"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"

    @graphql
    Scenario: Fetching wishlists
        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 0 wishlists

    @graphql
    Scenario: Fetching wishlists with two existing
        And user "aondra@climb.com" has a wishlist named "For me"
        And user "aondra@climb.com" has a wishlist named "For wife"
        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 2 wishlists
