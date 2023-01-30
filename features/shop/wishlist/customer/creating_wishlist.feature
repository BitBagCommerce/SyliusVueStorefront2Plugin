@wishlist_as_customer
Feature: Creating a wishlist
    In order to create a new wishlist
    As a customer
    I want to be able to create new wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For me"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"

    @graphql
    Scenario: Creating a wishlist
        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 0 wishlists
        When I prepare create wishlist operation
        And I set name field to "For me"
        And I set channelCode field to "WEB-US"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "wishlist.name" equal to "For me"
        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 1 wishlists

    @graphql
    Scenario: Creating a wishlist with a name that already exists
        And user "aondra@climb.com" has a wishlist named "For me"
        And user "aondra@climb.com" has a wishlist named "For wife"
        When I prepare create wishlist operation
        And I set name field to "For wife"
        And I set channelCode field to "WEB-US"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "extensions.message" equal to "name: The name has to be unique"

