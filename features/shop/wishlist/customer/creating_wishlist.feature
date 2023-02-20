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
        And I authorize as "aondra@climb.com"

    @graphql
    Scenario: Creating a wishlist
        Given There is operation to create wishlist
        And I set name field to "For me"
        And I set channelCode field to "WEB-US"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This wishlist should have name "For me"
        And This wishlist should have 0 products
        And user "aondra@climb.com" should have 1 wishlists

    @graphql
    Scenario: Creating a wishlist
        Given user "aondra@climb.com" has a wishlist named "For me"
        And There is operation to create wishlist
        And I set name field to "For wife"
        And I set channelCode field to "WEB-US"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This wishlist should have name "For wife"
        And This wishlist should have 0 products
        And user "aondra@climb.com" should have 2 wishlists

    @graphql
    Scenario: Creating a wishlist with a name that already exists
        Given user "aondra@climb.com" has a wishlist named "For me"
        And There is operation to create wishlist
        And I set name field to "For me"
        And I set channelCode field to "WEB-US"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        Then This response should contain message why the name cannot be changed

