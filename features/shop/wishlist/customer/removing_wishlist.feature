@wishlist_as_customer
Feature: Removing a wishlist
    In order to remove a wishlist
    As a customer
    I want to be able to remove wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For Alex"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And user "aondra@climb.com" has a wishlist named "For me"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"

    @graphql
    Scenario: Removing wishlists
        And user "aondra@climb.com" has a wishlist named "For wife"
        When I prepare remove wishlist operation
        And I set id field to iri object "For wife"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response

        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 1 wishlists
        And This response should contain "collection.0.name" equal to "For me"

        When I prepare remove wishlist operation
        And I set id field to iri object "For me"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response

        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 0 wishlists

    @graphql
    Scenario: Removing another user's wishlist
        When I prepare update wishlist operation
        And I set id field to iri object "For Alex"
        And I set name field to "For me"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive access denied
